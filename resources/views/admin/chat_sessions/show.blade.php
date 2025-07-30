@extends('admin.layouts.admin')

@section('title', 'Chat Session Details')

@section('breadcrumbs')
    <a href="{{ route('admin.chat_sessions.index') }}" class="hover:text-emerald-600">Chat Sessions</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700">Session #{{ $session->id }}</span>
@endsection

@section('content')
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Session details -->
            <div class="bg-white rounded-lg shadow-sm p-6 flex-1">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-xl font-semibold text-gray-800">{{ $session->name }}</h1>

                            <!-- Session Status Badge -->
                            @if($session->status === 'active')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @elseif($session->status === 'closed')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Closed
                                </span>
                            @elseif($session->status === 'flagged')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Flagged
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600">Session ID: #{{ $session->id }}</p>
                    </div>

                    <div class="flex space-x-2">
                        <form action="{{ route('admin.chat_sessions.destroy', $session->id) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this session? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <h2 class="text-sm font-medium text-gray-700">User Information</h2>
                        <p class="text-sm mt-1">Name: {{ $session->user->name }}</p>
                        <p class="text-sm">Email: {{ $session->user->email }}</p>
                    </div>
                    <div>
                        <h2 class="text-sm font-medium text-gray-700">Session Information</h2>
                        <p class="text-sm mt-1">Created: {{ $session->created_at->format('Y-m-d H:i:s') }}</p>
                        <p class="text-sm">Last activity: {{ $session->updated_at->format('Y-m-d H:i:s') }}</p>
                        <p class="text-sm">Messages: {{ $session->messages->count() }}</p>
                    </div>
                </div>

                <!-- Session Tags -->
                <div class="mb-4">
                    <h2 class="text-sm font-medium text-gray-700 mb-2">Tags</h2>
                    <div class="flex flex-wrap gap-2">
                        @forelse($session->tags ?? [] as $tag)
                            <div class="flex items-center bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-1 rounded">
                                {{ $tag }}
                                <form action="{{ route('admin.chat_sessions.remove-tag', $session->id) }}" method="POST" class="inline ml-1">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="tag" value="{{ $tag }}">
                                    <button type="submit" class="text-blue-800 hover:text-blue-900">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <span class="text-sm text-gray-500">No tags</span>
                        @endforelse

                        <!-- Add new tag form -->
                        <form action="{{ route('admin.chat_sessions.add-tag', $session->id) }}" method="POST" class="inline-flex">
                            @csrf
                            <input type="text" name="tag" placeholder="Add tag" class="rounded-l-md border-r-0 text-sm py-0.5 px-2 w-24">
                            <button type="submit" class="bg-blue-100 text-blue-800 rounded-r-md border border-blue-200 px-2 py-0.5 text-xs">
                                Add
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Action panel -->
            <div class="bg-white rounded-lg shadow-sm p-6 w-full md:w-80">
                <h2 class="text-sm font-medium text-gray-700 mb-3">Session Actions</h2>

                <div class="space-y-3">
                    <!-- Session Status Update -->
                    <div>
                        <h3 class="text-xs font-medium text-gray-600 mb-2">Update Status</h3>
                        <div class="flex space-x-2">
                            @if(!$session->isActive())
                                <form action="{{ route('admin.chat_sessions.reopen', $session->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-xs">
                                        <i class="fas fa-play mr-1"></i> Reopen
                                    </button>
                                </form>
                            @endif

                            @if(!$session->isClosed())
                                <form action="{{ route('admin.chat_sessions.close', $session->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded-md text-xs">
                                        <i class="fas fa-lock mr-1"></i> Close
                                    </button>
                                </form>
                            @endif

                            @if(!$session->isFlagged())
                                <form action="{{ route('admin.chat_sessions.flag', $session->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1.5 rounded-md text-xs">
                                        <i class="fas fa-flag mr-1"></i> Flag
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Rename Session -->
                    <div>
                        <h3 class="text-xs font-medium text-gray-600 mb-2">Rename Session</h3>
                        <form action="{{ route('admin.chat_sessions.update', $session->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="flex">
                                <input type="text" name="name" value="{{ $session->name }}"
                                       class="rounded-l-md border-r-0 w-full text-sm">
                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-r-md text-xs">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Export Transcript -->
                    <div>
                        <h3 class="text-xs font-medium text-gray-600 mb-2">Export Transcript</h3>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.chat_sessions.export', ['id' => $session->id, 'format' => 'csv']) }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs">
                                <i class="fas fa-file-csv mr-1"></i> CSV
                            </a>
                            <a href="{{ route('admin.chat_sessions.export', ['id' => $session->id, 'format' => 'pdf']) }}"
                               class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-xs">
                                <i class="fas fa-file-pdf mr-1"></i> PDF
                            </a>
                        </div>
                    </div>

                    <!-- Add Message (Impersonate) -->
                    <div>
                        <h3 class="text-xs font-medium text-gray-600 mb-2">Add Message (Impersonate)</h3>
                        <form action="{{ route('admin.chat_sessions.add-message', $session->id) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <select name="sender" class="w-full rounded-md text-sm py-1.5">
                                    <option value="user">User ({{ $session->user->name }})</option>
                                    <option value="bot">Bot</option>
                                </select>
                            </div>
                            <div class="flex">
                                <textarea name="content" placeholder="Message content"
                                          class="rounded-l-md border-r-0 w-full text-sm" rows="2"></textarea>
                                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-r-md text-xs">
                                    Send
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Transcript -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-medium text-gray-800 mb-4">Chat Transcript</h2>

            <div class="space-y-4 admin-view">
                @forelse($session->messages as $message)
                    <div class="flex {{ $message->sender === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="flex flex-col {{ $message->sender === 'user' ? 'items-end' : 'items-start' }}">
                            <!-- Message metadata -->
                            <div class="flex items-center text-xs text-gray-500 mb-1 {{ $message->sender === 'user' ? 'flex-row-reverse' : '' }}">
                                <span>{{ $message->sender === 'user' ? $session->user->name : 'Bot' }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ $message->created_at->format('M j, Y g:i A') }}</span>

                                <!-- Message actions -->
                                <div class="flex space-x-1 ml-2">
                                    <form action="{{ route('admin.chat_sessions.flag-message', ['sessionId' => $session->id, 'messageId' => $message->id]) }}"
                                          method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-800">
                                            <i class="fas fa-flag"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.chat_sessions.delete-message', ['sessionId' => $session->id, 'messageId' => $message->id]) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this message?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Message content -->
                            <div class="{{ $message->sender === 'user' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}
            rounded-lg px-4 py-2 max-w-lg break-words {{ $message->is_flagged ? 'border-2 border-yellow-400' : '' }}">
                                @if($message->sender === 'bot')
                                    {!! $message->content !!}
                                @else
                                    {!! nl2br(e($message->content)) !!}
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No messages in this chat session</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
