<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Display a listing of contacts
     */
    public function index(Request $request)
    {
        $query = Contact::with('user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate stats
        $stats = [
            'total' => Contact::count(),
            'new' => Contact::where('status', 'new')->count(),
            'read' => Contact::where('status', 'read')->count(),
            'replied' => Contact::where('status', 'replied')->count(),
        ];

        return view('admin.contacts.index', compact('contacts', 'stats'));
    }

    /**
     * Display a single contact
     */
    public function show(Contact $contact)
    {
        // Load user relationship
        $contact->load('user', 'respondedByUser');

        // Mark as read if it was new
        if ($contact->status === 'new') {
            try {
                $contact->status = 'read';
                $contact->save();
            } catch (\Exception $e) {
                \Log::error('Error updating contact status:', [
                    'contact_id' => $contact->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Update contact status
     */
    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,read,replied',
            'response' => 'nullable|string|min:0',  // Allow empty responses
        ]);

        // Prepare update data
        $updateData = [
            'status' => $validated['status'],
        ];

        // Add response if provided
        if (!empty($validated['response'])) {
            $updateData['response'] = $validated['response'];
        }

        // If status is being changed to replied, record responder info
        if ($validated['status'] === 'replied' && $contact->status !== 'replied') {
            $updateData['responded_by'] = auth()->user()->id;
            $updateData['responded_at'] = now();
        }

        $contact->update($updateData);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công',
                'data' => $contact,
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công');
    }

    /**
     * Delete a contact
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Xóa tin nhắn thành công');
    }

    /**
     * Send reply email to contact
     */
    public function sendReplyEmail(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'reply_message' => 'required|string|min:5',
        ]);

        try {
            $customerName = $contact->name;
            $customerEmail = $contact->email;
            $message = $validated['reply_message'];

            // Send email
            Mail::send([], [], function ($mail) use ($customerName, $customerEmail, $message, $contact) {
                $mail->from(config('mail.from.address'), config('mail.from.name'))
                     ->to($customerEmail)
                     ->subject('Phản hồi từ Travel App - ' . $contact->subject)
                     ->html(
                        "<p>Xin chào <strong>{$customerName}</strong>,</p>" .
                        "<p>Cảm ơn bạn đã liên hệ với chúng tôi!</p>" .
                        "<p>Dưới đây là phản hồi từ đội ngũ Travel App:</p>" .
                        "<hr>" .
                        "<div style='background-color: #f5f5f5; padding: 15px; border-left: 4px solid #007bff;'>" .
                        "<p style='white-space: pre-wrap;'>" . htmlspecialchars($message) . "</p>" .
                        "</div>" .
                        "<hr>" .
                        "<p>Nếu bạn có bất kỳ câu hỏi nào khác, vui lòng liên hệ lại.</p>" .
                        "<p>Trân trọng,<br><strong>Đội ngũ Travel App</strong></p>"
                     );
            });

            // Update contact status
            $contact->update([
                'status' => 'replied',
                'response' => $message,
                'responded_by' => auth()->user()->id,
                'responded_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Email phản hồi đã gửi thành công tới ' . $customerEmail);
        } catch (\Exception $e) {
            \Log::error('Error sending contact reply email:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Lỗi khi gửi email: ' . $e->getMessage());
        }
    }

    /**
     * Send cancellation email with refund notification
     */
    public function sendCancellationEmail(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'refund_amount' => 'required|numeric|min:0',
            'cancellation_reason' => 'required|string|min:10',
        ]);

        try {
            // Prepare email data
            $customerName = $contact->name;
            $customerEmail = $contact->email;
            $refundAmount = $validated['refund_amount'];
            $message = $validated['cancellation_reason'];
            $refundDate = now()->addDays(3)->format('d/m/Y');

            // Send email using Mail facade with view
            Mail::send('emails.cancellation-notification', [
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'subject' => 'Thông báo hủy đơn hàng và hoàn tiền',
                'cancellation_reason' => $message,
                'refund_amount' => $refundAmount,
                'refund_date' => $refundDate,
            ], function ($message) use ($customerEmail) {
                $message->from(config('mail.from.address'), config('mail.from.name'))
                        ->to($customerEmail)
                        ->subject('Thông báo hủy đơn hàng và hoàn tiền');
            });

            // Update contact with cancellation info
            $contact->update([
                'status' => 'replied',
                'response' => "Hủy đơn - Hoàn tiền: " . $refundAmount . " VND. Lý do: " . $message,
                'responded_by' => auth()->user()->id,
                'responded_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Email hủy đơn và hoàn tiền đã được gửi thành công tới ' . $customerEmail);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi gửi email: ' . $e->getMessage());
        }
    }
}
