@extends('admin.layouts.admin')

@section('title', 'View AliExpress Chat Session')

@section('breadcrumbs')
    <a href="{{ route('admin.aliexpress.chat_sessions.index') }}" class="text-gray-700 hover:text-emerald-600">AliExpress Chat Sessions</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700">Session #{{ $session->id }}</span>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">AliExpress Chat Session #{{ $session->id }}</h2>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.aliexpress.chat_sessions.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Session Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Session ID</label>
                        <p class="text-lg font-semibold text-gray-800">#{{ $session->id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">User</label>
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                                <i class="fas fa-user text-emerald-600"></i>
                            </div>
                            <div>
                                <p class="text-lg text-gray-800">{{ $session->user->name ?? 'Guest User' }}</p>
                                @if($session->user)
                                    <p class="text-sm text-gray-500">{{ $session->user->email }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                            {{ $session->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($session->status) }}
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created</label>
                        <p class="text-lg text-gray-800">{{ $session->created_at->format('M d, Y H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Last Activity</label>
                        <p class="text-lg text-gray-800">{{ $session->updated_at->format('M d, Y H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Duration</label>
                        <p class="text-lg text-gray-800">{{ $session->created_at->diffForHumans($session->updated_at, true) }}</p>
                    </div>
                </div>
            </div>

            <!-- Session Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="rounded-full bg-blue-100 p-2 mr-3">
                            <i class="fas fa-comments text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Messages</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $session->messages->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-emerald-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="rounded-full bg-emerald-100 p-2 mr-3">
                            <i class="fas fa-search text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Products Searched</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $session->products_searched ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="rounded-full bg-purple-100 p-2 mr-3">
                            <i class="fas fa-bookmark text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Products Saved</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $session->products_saved ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
            @if(isset($session->messages) && $session->messages->count() > 0)
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Chat Messages</h3>
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @foreach($session->messages as $message)
                    <div class="flex {{ $message->sender == 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg
                            {{ $message->sender == 'user' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-800' }}">
                            <div class="flex items-center mb-1">
                                <span class="font-medium text-sm">
                                    {{ $message->sender == 'user' ? 'User' : ucfirst($message->sender) }}
                                </span>
                                <span class="text-xs ml-2 opacity-75">
                                    {{ $message->created_at->format('H:i') }}
                                </span>
                            </div>
                            <p class="text-sm">{{ $message->content }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="border-t border-gray-200 pt-6">
                <div class="text-center py-8">
                    <div class="text-gray-400 text-4xl mb-3">
                        <i class="fas fa-comment-slash"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No messages yet</h3>
                    <p class="text-gray-500 mt-1">Messages will appear here when the user starts chatting</p>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <button type="button"
                        onclick="confirmDelete({{ $session->id }})"
                        class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md transition-all">
                    <i class="fas fa-trash mr-2"></i> Delete Session
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center hidden">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="text-center mb-4">
                <div id="modal-icon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i id="modal-icon-i" class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 id="modal-title" class="text-lg leading-6 font-medium text-gray-900">Confirm Delete</h3>
                <div class="mt-2">
                    <p id="modal-message" class="text-sm text-gray-500">Are you sure you want to delete this chat session? This action cannot be undone.</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="closeConfirmModal()" class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" id="confirm-button" class="bg-red-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-red-700">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden delete form -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('scripts')
<script>
    // Confirmation modal functions
    function confirmDelete(id) {
        const modal = document.getElementById('confirm-modal');
        const confirmButton = document.getElementById('confirm-button');

        // Set confirm action
        confirmButton.onclick = function() {
            const form = document.getElementById('delete-form');
            form.action = '/admin/aliexpress/chat-sessions/' + id;
            form.submit();
        };

        // Show modal
        modal.classList.add('flex');
        modal.classList.remove('hidden');
    }

    function closeConfirmModal() {
        const modal = document.getElementById('confirm-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close modal when clicking outside
    document.getElementById('confirm-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeConfirmModal();
        }
    });
</script>
@endsection
