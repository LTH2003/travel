<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

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

        return redirect()->back()->with('success', 'Xóa tin nhắn thành công');
    }
}
