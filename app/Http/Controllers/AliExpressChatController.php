<?php

namespace App\Http\Controllers;

use App\Models\AliExpressChatMessage;
use App\Models\AliExpressChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AliExpressChatController extends Controller
{
    /**
     * Get all chat sessions for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSessions()
    {
        $sessions = AliExpressChatSession::where('user_id', Auth::id())
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
            Log::info('Creating AliExpress chat session', [
                'user_id' => $userId,
                'name' => $request->name
            ]);

            if (!$userId) {
                Log::error('Failed to create AliExpress chat session: No authenticated user');
                return response()->json([
                    'details' => 'User not authenticated'
                ], 401);
            }

            $session = AliExpressChatSession::create([
                'user_id' => $userId,
                'name' => $request->name,
            ]);

            Log::info('AliExpress chat session created successfully', ['session_id' => $session->id]);

            return response()->json([
                'message' => 'AliExpress chat session created successfully.',
                'session' => $session,
                'id' => $session->id
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create AliExpress chat session', [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new message in an existing chat session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMessage(Request $request)
    {
        $request->validate([
            'session_id' => [
                'required',
                Rule::exists('aliexpress_chat_sessions', 'id')->where(function ($query) {
                    $query->where('user_id', Auth::id());
                }),
            ],
            'content' => 'required|string',
            'sender' => 'required|in:user,bot',
        ]);

        $session = AliExpressChatSession::findOrFail($request->session_id);

        // Ensure user can only add messages to their own sessions
        if ($session->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the last order number for this session
        $lastMessage = AliExpressChatMessage::where('session_id', $session->id)
            ->orderBy('order', 'desc')
            ->first();

        $order = $lastMessage ? $lastMessage->order + 1 : 1;

        $message = AliExpressChatMessage::create([
            'session_id' => $session->id,
            'content' => $request->content,
            'sender' => $request->sender,
            'order' => $order,
        ]);

        // Update the session's updated_at timestamp
        $session->touch();

        return response()->json([
            'message' => $message,
            'status' => 'Message added successfully.'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($sessionId)
    {
        $session = AliExpressChatSession::where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $messages = $session->messages()->get();

        return response()->json([
            'session' => $session,
            'messages' => $messages
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $sessionId)
    {
        $session = AliExpressChatSession::where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $session->update([
            'name' => $request->name
        ]);

        return response()->json([
            'session' => $session,
            'message' => 'Session updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($sessionId)
    {
        $session = AliExpressChatSession::where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        // Delete the session (messages will be cascade deleted due to foreign key constraint)
        $session->delete();

        return response()->json([
            'message' => 'Session deleted successfully.'
        ]);
    }

    /**
     * Simple ping endpoint for testing connection.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ping()
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'AliExpress Chat API is working'
        ]);
    }

    /**
     * Clear all chat history for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearHistory()
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Delete all sessions for this user
        AliExpressChatSession::where('user_id', $userId)->delete();

        return response()->json([
            'message' => 'History cleared successfully'
        ]);
    }
}
