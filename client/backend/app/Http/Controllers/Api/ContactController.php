<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Store a contact message (requires authentication via route middleware)
     */
    public function store(Request $request)
    {
        try {
            // Route middleware ensures user is authenticated
            $user = $request->user();
            
            \Log::info('Contact store - User:', ['user_id' => $user?->id, 'name' => $user?->name]);

            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string|min:10',
            ]);

            \Log::info('Contact validated:', $validated);

            // Create contact with user info from authenticated user
            $contact = Contact::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'status' => 'new',
            ]);

            \Log::info('Contact created:', ['id' => $contact->id, 'user_id' => $contact->user_id]);

            return response()->json([
                'status' => true,
                'message' => 'Tin nhắn đã được gửi thành công!',
                'data' => $contact,
            ]);
        } catch (\Exception $e) {
            \Log::error('Contact submission error:', [
                'message' => $e->getMessage(),
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get all contacts (admin only)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $status = $request->query('status');
        $query = Contact::query();

        if ($status) {
            $query->where('status', $status);
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'status' => true,
            'data' => $contacts,
        ]);
    }

    /**
     * Get a single contact (admin only)
     */
    public function show(Contact $contact, Request $request)
    {
        $user = $request->user();
        
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Mark as read
        $contact->update(['status' => 'read']);

        return response()->json([
            'status' => true,
            'data' => $contact,
        ]);
    }

    /**
     * Update contact status (admin only)
     */
    public function update(Contact $contact, Request $request)
    {
        $user = $request->user();
        
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

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
            $updateData['responded_by'] = $user->id;
            $updateData['responded_at'] = now();
        }

        $contact->update($updateData);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thành công',
            'data' => $contact,
        ]);
    }

    /**
     * Delete a contact (admin only)
     */
    public function destroy(Contact $contact, Request $request)
    {
        $user = $request->user();
        
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $contact->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa thành công',
        ]);
    }
}
