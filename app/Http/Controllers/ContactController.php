<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Submit a contact message (Public).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:150',
            'message' => 'required|string|min:10|max:3000',
        ]);

        $message = Message::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Your message has been sent successfully.',
            'data' => $message,
        ], 201);
    }

    /**
     * Retrieve all messages (Admin only).
     */
    public function index(): JsonResponse
    {
        $messages = Message::latest()->get();

        return response()->json($messages);
    }

    /**
     * Mark a message as read or toggle status (Admin only).
     */
    public function markAsRead(int $id): JsonResponse
    {
        $message = Message::find($id);

        if (! $message) {
            return response()->json([
                'message' => 'Message not found.',
            ], 404);
        }

        $message->update(['is_read' => true]);

        return response()->json([
            'message' => 'Message marked as read.',
            'data' => $message,
        ]);
    }

    /**
     * Delete a contact message (Admin only).
     */
    public function destroy(int $id): JsonResponse
    {
        $message = Message::find($id);

        if (! $message) {
            return response()->json([
                'message' => 'Message not found.',
            ], 404);
        }

        $message->delete();

        return response()->json([
            'message' => 'Message deleted successfully.',
        ]);
    }
}
