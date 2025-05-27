<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatSessionController extends Controller
{
    /**
     * Display the chat interface view.
     *
     * @return \Illuminate\View\View
     */
    public function showChatInterface()
    {
        return view('chatbot');
    }

    /**
     * Get all chat sessions for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSessions()
    {
        $sessions = ChatSession::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'sessions' => $sessions
        ]);
    }

    /**
     * Store a new chat session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSession(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            // Get the authenticated user ID
            $userId = Auth::id();

            // Debug logging
            Log::info('Creating chat session', [
                'user_id' => $userId,
                'name' => $request->name
            ]);

            if (!$userId) {
                Log::error('Failed to create chat session: No authenticated user');
                return response()->json([
                    'error' => 'Authentication required',
                    'details' => 'User not authenticated'
                ], 401);
            }

            $session = ChatSession::create([
                'user_id' => $userId,
                'name' => $request->name,
            ]);

            Log::info('Chat session created successfully', ['session_id' => $session->id]);

            return response()->json([
                'session' => $session,
                'message' => 'Chat session created successfully.'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Exception creating chat session', ['exception' => $e->getMessage()]);
            return response()->json([
                'error' => 'Server error',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new message in a chat session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMessage(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:chat_sessions,id',
            'content' => 'required|string',
            'sender' => 'required|in:user,bot',
        ]);

        $session = ChatSession::findOrFail($request->session_id);

        // Ensure user can only add messages to their own sessions
        if ($session->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the last order number for this session
        $lastMessage = ChatMessage::where('session_id', $session->id)
            ->orderBy('order', 'desc')
            ->first();

        $order = $lastMessage ? $lastMessage->order + 1 : 1;

        $message = ChatMessage::create([
            'session_id' => $session->id,
            'content' => $request->content,
            'sender' => $request->sender,
            'order' => $order,
        ]);

        // Update the session's updated_at timestamp
        $session->touch();

        return response()->json([
            'message' => $message,
            'session' => $session
        ]);
    }

    /**
     * Get all messages for a chat session.
     *
     * @param  int  $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($sessionId)
    {
        $session = ChatSession::findOrFail($sessionId);

        // Ensure user can only view their own sessions
        if ($session->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = ChatMessage::where('session_id', $sessionId)
            ->orderBy('order', 'asc')
            ->get();

        return response()->json([
            'session' => $session,
            'messages' => $messages
        ]);
    }

    /**
     * Update a chat session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $sessionId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $session = ChatSession::findOrFail($sessionId);

        // Ensure user can only update their own sessions
        if ($session->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $session->name = $request->name;
        $session->save();

        return response()->json([
            'session' => $session,
            'message' => 'Chat session updated successfully'
        ]);
    }

    /**
     * Delete a chat session.
     *
     * @param  int  $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($sessionId)
    {
        $session = ChatSession::findOrFail($sessionId);

        // Ensure user can only delete their own sessions
        if ($session->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete all messages in this session
        ChatMessage::where('session_id', $sessionId)->delete();

        // Delete the session
        $session->delete();

        return response()->json([
            'message' => 'Chat session deleted successfully'
        ]);
    }
}
