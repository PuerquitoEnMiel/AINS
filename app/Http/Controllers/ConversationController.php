<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    /**
     * List all conversations for the authenticated user.
     */
    public function index()
    {
        $conversations = Auth::user()->conversations()
            ->with('lastMessage')
            ->latest()
            ->get();

        return response()->json($conversations);
    }

    /**
     * Create a new conversation.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
        ]);

        $conversation = Auth::user()->conversations()->create([
            'title' => $data['title'] ?? 'Nueva conversación',
        ]);

        return response()->json($conversation, 201);
    }

    /**
     * Get messages for a conversation.
     */
    public function show(ChatConversation $conversation)
    {
        // Ensure user owns this conversation
        if ($conversation->user_id !== Auth::id()) {
            abort(403);
        }

        $messages = $conversation->messages()->orderBy('created_at')->get();

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    /**
     * Rename a conversation.
     */
    public function update(Request $request, ChatConversation $conversation)
    {
        if ($conversation->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate(['title' => 'required|string|max:255']);
        $conversation->update($data);

        return response()->json($conversation);
    }

    /**
     * Delete a conversation and its messages.
     */
    public function destroy(ChatConversation $conversation)
    {
        if ($conversation->user_id !== Auth::id()) {
            abort(403);
        }

        $conversation->delete();

        return response()->json(['message' => 'Conversación eliminada.']);
    }
}
