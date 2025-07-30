<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AliExpressChatSession;
use App\Models\AliExpressChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AliExpressChatSessionController extends Controller
{
    /**
     * Display a listing of AliExpress chat sessions.
     */
    public function index(Request $request)
    {
        // Get chat sessions from aliexpress_chat_sessions table
        $query = AliExpressChatSession::with(['user', 'messages']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Filter by session activity
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->where('status', AliExpressChatSession::STATUS_ACTIVE);
            } elseif ($status === 'inactive') {
                $query->where('status', AliExpressChatSession::STATUS_CLOSED);
            } elseif ($status === 'flagged') {
                $query->where('status', AliExpressChatSession::STATUS_FLAGGED);
            }
        }

        // Order by most recent activity
        $query->orderBy('updated_at', 'desc');

        $sessions = $query->paginate(20);

        // Transform data to include session information
        $sessions->getCollection()->transform(function ($session) {
            $session->message_count = $session->messages->count();
            $session->last_activity = $session->updated_at;
            return $session;
        });

        return view('admin.aliexpress.chat-sessions.index', compact('sessions'));
    }

    /**
     * Display the specified chat session.
     */
    public function show($id)
    {
        $session = AliExpressChatSession::with(['user', 'messages'])->findOrFail($id);

        return view('admin.aliexpress.chat-sessions.show', compact('session'));
    }

    /**
     * Remove the specified chat session.
     */
    public function destroy($id)
    {
        $session = AliExpressChatSession::findOrFail($id);

        // Delete all messages first (cascade should handle this, but being explicit)
        $session->messages()->delete();

        // Delete the session
        $session->delete();

        return redirect()->route('admin.aliexpress.chat_sessions.index')
            ->with('success', 'AliExpress chat session deleted successfully.');
    }

    /**
     * Clear a specific chat session.
     */
    public function clear($id)
    {
        $session = AliExpressChatSession::findOrFail($id);

        // Delete all messages in this session
        $session->messages()->delete();

        return redirect()->route('admin.aliexpress.chat_sessions.show', $id)
            ->with('success', 'AliExpress chat session messages cleared successfully.');
    }

    /**
     * Export chat sessions to CSV.
     */
    public function export(Request $request)
    {
        $query = AliExpressChatSession::with(['user', 'messages']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->where('status', AliExpressChatSession::STATUS_ACTIVE);
            } elseif ($status === 'inactive') {
                $query->where('status', AliExpressChatSession::STATUS_CLOSED);
            } elseif ($status === 'flagged') {
                $query->where('status', AliExpressChatSession::STATUS_FLAGGED);
            }
        }

        $sessions = $query->orderBy('updated_at', 'desc')->get();

        $filename = 'aliexpress_chat_sessions_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($sessions) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Session ID',
                'Session Name',
                'User ID',
                'User Name',
                'Email',
                'Status',
                'Message Count',
                'Tags',
                'Last Activity',
                'Created At'
            ]);

            foreach ($sessions as $session) {
                fputcsv($file, [
                    $session->id,
                    $session->name,
                    $session->user->id,
                    $session->user->name,
                    $session->user->email,
                    $session->status,
                    $session->messages->count(),
                    implode(', ', $session->tags ?? []),
                    $session->updated_at,
                    $session->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Add a message to a chat session.
     */
    public function addMessage(Request $request, $id)
    {
        $session = AliExpressChatSession::findOrFail($id);

        $request->validate([
            'content' => 'required|string',
            'sender' => 'required|in:user,bot,admin',
        ]);

        // Get the next order number
        $nextOrder = $session->messages()->max('order') + 1;

        AliExpressChatMessage::create([
            'session_id' => $session->id,
            'content' => $request->content,
            'sender' => $request->sender,
            'order' => $nextOrder,
        ]);

        return redirect()->route('admin.aliexpress.chat_sessions.show', $id)
            ->with('success', 'Message added to chat session successfully.');
    }

    /**
     * Delete a specific message from chat session.
     */
    public function deleteMessage($sessionId, $messageId)
    {
        $session = AliExpressChatSession::findOrFail($sessionId);
        $message = AliExpressChatMessage::where('session_id', $sessionId)
            ->where('id', $messageId)
            ->firstOrFail();

        $message->delete();

        return redirect()->route('admin.aliexpress.chat_sessions.show', $sessionId)
            ->with('success', 'Message deleted successfully.');
    }
}
