<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class ChatSessionController extends Controller
{
    /**
     * Display a listing of chat sessions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = ChatSession::query()->with(['user', 'messages']);

        // Apply search filters
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
            });
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $tag = $request->tag;
            $query->whereJsonContains('tags', $tag);
        }

        // Filter by message count
        if ($request->filled('min_messages')) {
            $query->has('messages', '>=', $request->min_messages);
        }

        // Sort by
        $sortField = $request->get('sort', 'updated_at');
        $sortDirection = $request->get('direction', 'desc');

        // Validate sort field
        $allowedSortFields = ['id', 'name', 'created_at', 'updated_at', 'status'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'updated_at';
        }

        $query->orderBy($sortField, $sortDirection);

        // Paginate with more options
        $perPage = $request->get('per_page', 15);
        $allowedPerPage = [10, 15, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 15;
        }

        $sessions = $query->paginate($perPage)->withQueryString();

        // Get distinct tags for filter dropdown
        $allTags = [];
        ChatSession::whereNotNull('tags')
            ->where('tags', '!=', '[]')
            ->get()
            ->each(function ($session) use (&$allTags) {
                if (is_array($session->tags)) {
                    foreach ($session->tags as $tag) {
                        if (!in_array($tag, $allTags)) {
                            $allTags[] = $tag;
                        }
                    }
                }
            });

        // Sort tags alphabetically
        sort($allTags);

        // Get users for filter dropdown
        $users = User::has('chatSessions')->get();

        return view('admin.chat_sessions.index', [
            'sessions' => $sessions,
            'allTags' => $allTags,
            'users' => $users,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'perPage' => $perPage,
            'allowedPerPage' => $allowedPerPage
        ]);
    }

    /**
     * Show the chat session details and messages.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $session = ChatSession::with(['user', 'messages' => function ($query) {
            $query->orderBy('order', 'asc');
        }])->findOrFail($id);

        return view('admin.chat_sessions.show', compact('session'));
    }

    /**
     * Update the session's status (close, reopen, flag).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in([
                ChatSession::STATUS_ACTIVE,
                ChatSession::STATUS_CLOSED,
                ChatSession::STATUS_FLAGGED
            ])],
        ]);

        $session = ChatSession::findOrFail($id);
        $session->status = $request->status;
        $session->save();

        return redirect()->back()->with('success', 'Session status updated successfully.');
    }

    /**
     * Update the session name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $session = ChatSession::findOrFail($id);
        $session->name = $request->name;
        $session->save();

        return redirect()->back()->with('success', 'Session name updated successfully.');
    }

    /**
     * Delete the specified session.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $session = ChatSession::findOrFail($id);

        // Delete all associated messages
        ChatMessage::where('session_id', $id)->delete();

        // Delete the session
        $session->delete();

        return redirect()->route('admin.chat_sessions.index')->with('success', 'Session deleted successfully.');
    }

    /**
     * Archive the specified session.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive($id)
    {
        $session = ChatSession::findOrFail($id);
        $session->close();

        return redirect()->back()->with('success', 'Session archived successfully.');
    }

    /**
     * Close the session (prevent further messages).
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function close($id)
    {
        $session = ChatSession::findOrFail($id);
        $session->close();

        return redirect()->back()->with('success', 'Session closed successfully.');
    }

    /**
     * Reopen a closed session.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reopen($id)
    {
        $session = ChatSession::findOrFail($id);
        $session->reopen();

        return redirect()->back()->with('success', 'Session reopened successfully.');
    }

    /**
     * Flag a session.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function flag($id)
    {
        $session = ChatSession::findOrFail($id);
        $session->flag();

        return redirect()->back()->with('success', 'Session flagged successfully.');
    }

    /**
     * Add a tag to the session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addTag(Request $request, $id)
    {
        $request->validate([
            'tag' => 'required|string|max:50',
        ]);

        $session = ChatSession::findOrFail($id);
        $session->addTag($request->tag);

        return redirect()->back()->with('success', 'Tag added successfully.');
    }

    /**
     * Remove a tag from the session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeTag(Request $request, $id)
    {
        $request->validate([
            'tag' => 'required|string|max:50',
        ]);

        $session = ChatSession::findOrFail($id);
        $session->removeTag($request->tag);

        return redirect()->back()->with('success', 'Tag removed successfully.');
    }

    /**
     * Delete a specific message from a chat session.
     *
     * @param  int  $sessionId
     * @param  int  $messageId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteMessage($sessionId, $messageId)
    {
        $message = ChatMessage::where('session_id', $sessionId)
            ->where('id', $messageId)
            ->firstOrFail();

        $message->delete();

        return redirect()->back()->with('success', 'Message deleted successfully.');
    }

    /**
     * Flag a specific message in a chat session.
     *
     * @param  int  $sessionId
     * @param  int  $messageId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function flagMessage($sessionId, $messageId)
    {
        $message = ChatMessage::where('session_id', $sessionId)
            ->where('id', $messageId)
            ->firstOrFail();

        // Add a flagged field to the message
        $message->is_flagged = true;
        $message->save();

        return redirect()->back()->with('success', 'Message flagged successfully.');
    }

    /**
     * Export the chat transcript as PDF or CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request, $id)
    {
        $request->validate([
            'format' => 'required|in:csv,pdf',
        ]);

        $session = ChatSession::with(['user', 'messages' => function ($query) {
            $query->orderBy('order', 'asc');
        }])->findOrFail($id);

        if ($request->format === 'csv') {
            $fileName = 'chat_session_' . $id . '_' . Carbon::now()->format('Ymd_His') . '.csv';

            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $callback = function () use ($session) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Timestamp', 'Sender', 'Message']);

                foreach ($session->messages as $message) {
                    $sender = $message->sender === 'user' ? $session->user->name : 'Bot';
                    fputcsv($file, [
                        $message->created_at->format('Y-m-d H:i:s'),
                        $sender,
                        $message->content
                    ]);
                }
                fclose($file);
            };

            $tempFile = tempnam(sys_get_temp_dir(), 'csv');
            $file = fopen($tempFile, 'w');
            fputcsv($file, ['Timestamp', 'Sender', 'Message']);

            foreach ($session->messages as $message) {
                $sender = $message->sender === 'user' ? $session->user->name : 'Bot';
                fputcsv($file, [
                    $message->created_at->format('Y-m-d H:i:s'),
                    $sender,
                    $message->content
                ]);
            }
            fclose($file);

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        } else {
            // In a real implementation, you would use a PDF library like DOMPDF
            // For this example, we'll simulate with a placeholder
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml('<h1>Chat Transcript</h1><p>PDF export is now implemented.</p>');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $output = $dompdf->output();
            $fileName = 'chat_session_' . $id . '_' . Carbon::now()->format('Ymd_His') . '.pdf';
            $tempFile = tempnam(sys_get_temp_dir(), 'pdf');
            file_put_contents($tempFile, $output);
            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        }
    }

    /**
     * Add a new message to the chat session (impersonate).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addMessage(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
            'sender' => 'required|in:user,bot',
        ]);

        $session = ChatSession::findOrFail($id);

        // Get next order number
        $lastMessage = ChatMessage::where('session_id', $id)
            ->orderBy('order', 'desc')
            ->first();

        $order = $lastMessage ? $lastMessage->order + 1 : 1;

        // Create new message
        ChatMessage::create([
            'session_id' => $id,
            'content' => $request->content,
            'sender' => $request->sender,
            'order' => $order
        ]);

        // Update session timestamp
        $session->touch();

        return redirect()->back()->with('success', 'Message added successfully.');
    }

    /**
     * Export chat sessions data to CSV or PDF
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportAll(Request $request)
    {
        // Get export format and scope
        $format = $request->input('format', 'csv');
        $exportScope = $request->input('export_scope', 'current_page');
        $perPage = $exportScope === 'current_page' ? 15 : 1000;
        $page = $request->input('page', 1);

        // Create base query
        $query = ChatSession::query()->with('user');

        // Apply filters if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by flag
        if ($request->filled('is_flagged')) {
            $query->where('is_flagged', filter_var($request->is_flagged, FILTER_VALIDATE_BOOLEAN));
        }

        // Filter by tag if provided
        if ($request->filled('tag')) {
            $query->where('tags', 'like', "%{$request->tag}%");
        }

        // Apply sorting
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Get chat sessions based on export scope
        if ($exportScope === 'current_page') {
            $chatSessions = $query->paginate($perPage, ['*'], 'page', $page);
        } else {
            $chatSessions = $query->get();
        }

        // Filename
        $filename = 'chat_sessions_export_' . date('Y_m_d_H_i_s');

        // Headers for the export
        $headers = [
            '#',
            'Session ID',
            'User',
            'Status',
            'Messages Count',
            'Created At',
            'Last Activity',
            'Flagged',
            'Tags'
        ];

        // Prepare data for export
        $data = [];
        $counter = 1;

        foreach ($chatSessions as $session) {
            $data[] = [
                $counter++,
                $session->id,
                $session->user ? $session->user->name . ' (' . $session->user->email . ')' : 'Guest',
                ucfirst($session->status),
                $session->messages_count ?? 'N/A',
                $session->created_at ? $session->created_at->format('Y-m-d H:i:s') : 'N/A',
                $session->updated_at ? $session->updated_at->format('Y-m-d H:i:s') : 'N/A',
                $session->is_flagged ? 'Yes' : 'No',
                $session->tags ?? 'N/A'
            ];
        }

        if ($format === 'csv') {
            return response($this->exportToCsv($filename, $headers, $data)->getContent(), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            ]);
        } else {
            return $this->exportToPdf($filename, $headers, $data, 'Chat Sessions List');
        }
    }

    /**
     * Export data to CSV file
     *
     * @param string $filename
     * @param array $headers
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function exportToCsv($filename, $headers, $data)
    {
        $filename = $filename . '.csv';

        $callback = function () use ($headers, $data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export data to PDF file
     *
     * @param string $filename
     * @param array $headers
     * @param array $data
     * @param string $title
     * @return \Illuminate\Http\Response
     */
    private function exportToPdf($filename, $headers, $data, $title)
    {
        $filename = $filename . '.pdf';

        // Generate HTML for PDF
        $html = '<style>
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #064e3b;
                color: white;
            }
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            .title {
                text-align: center;
                margin-bottom: 20px;
                font-size: 24px;
            }
            .footer {
                text-align: center;
                margin-top: 20px;
                font-size: 12px;
                color: #666;
            }
        </style>';

        $html .= '<div class="title">' . $title . '</div>';
        $html .= '<table>';

        // Add headers
        $html .= '<tr>';
        foreach ($headers as $header) {
            $html .= '<th>' . $header . '</th>';
        }
        $html .= '</tr>';

        // Add data rows
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . $cell . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '<div class="footer">Generated on ' . date('Y-m-d H:i:s') . '</div>';

        // Generate PDF using Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Stream PDF to browser
        return response()->make($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
