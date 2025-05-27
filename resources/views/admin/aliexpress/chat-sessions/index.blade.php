@extends('admin.layouts.admin')

@section('title', 'AliExpress Chat Sessions')

@section('breadcrumbs')
    <span class="text-gray-500">AliExpress</span>
    <span class="mx-2">/</span>
    <span class="text-gray-900">Chat Sessions</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">AliExpress Chat Sessions</h1>
            <p class="text-gray-600 text-sm mt-1">Manage user chat sessions for AliExpress</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.aliexpress.chat_sessions.export', request()->query()) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center text-sm">
                <i class="fas fa-download mr-2"></i>
                Export
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-comments text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Total Sessions</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $sessions->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-clock text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Active Today</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $sessions->filter(function($session) { return $session->updated_at->isToday(); })->count() }}
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-message text-yellow-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Avg Messages</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $sessions->avg('message_count') ? number_format($sessions->avg('message_count'), 1) : 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-users text-purple-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Active Users</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $sessions->filter(function($session) { return $session->updated_at->gte(now()->subDay()); })->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.aliexpress.chat_sessions.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="User name or email..."
                           class="w-full">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full">
                        <option value="">All Sessions</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active (24h)</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive (>24h)</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <div class="flex space-x-3 w-full">
                        <button type="submit" class="filter-btn bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.aliexpress.chat_sessions.index') }}"
                           class="filter-btn bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Clear
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Chat Sessions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Messages</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                        <i class="fas fa-user text-emerald-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $session->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $session->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $session->message_count }} messages
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $session->last_activity->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($session->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-green-400 mr-1" style="font-size: 0.5rem;"></i>
                                        Active
                                    </span>
                                @elseif($session->status === 'flagged')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-flag text-red-400 mr-1" style="font-size: 0.5rem;"></i>
                                        Flagged
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-circle text-gray-400 mr-1" style="font-size: 0.5rem;"></i>
                                        Closed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $session->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('admin.aliexpress.chat_sessions.show', $session->id) }}"
                                   class="text-blue-600 hover:text-blue-900" title="View Session">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button"
                                        onclick="confirmAction({{ $session->id }}, 'clear')"
                                        class="text-yellow-600 hover:text-yellow-900"
                                        title="Clear Session">
                                    <i class="fas fa-broom"></i>
                                </button>
                                <button type="button"
                                        onclick="confirmAction({{ $session->id }}, 'delete')"
                                        class="text-red-600 hover:text-red-900"
                                        title="Delete Session">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No AliExpress chat sessions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($sessions->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200">
                {{ $sessions->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

    <!-- Confirmation Modal -->
    <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center hidden">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="text-center mb-4">
                <div id="modal-icon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i id="modal-icon-i" class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 id="modal-title" class="text-lg leading-6 font-medium text-gray-900">Confirm Action</h3>
                <div class="mt-2">
                    <p id="modal-message" class="text-sm text-gray-500">Are you sure you want to perform this action?</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="closeConfirmModal()" class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" id="confirm-button" class="bg-red-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-red-700">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden forms -->
    <form id="clear-form" method="POST" style="display: none;">
        @csrf
    </form>

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('scripts')
<script>
    // Confirmation modal functions
    function confirmAction(id, action) {
        const modal = document.getElementById('confirm-modal');
        const confirmButton = document.getElementById('confirm-button');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalIcon = document.getElementById('modal-icon-i');

        if (action === 'clear') {
            modalTitle.textContent = 'Confirm Clear';
            modalMessage.textContent = 'Are you sure you want to clear this chat session? All messages will be removed.';
            modalIcon.className = 'fas fa-broom text-yellow-600';
            document.getElementById('modal-icon').className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4';
            confirmButton.className = 'bg-yellow-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-yellow-700';
            confirmButton.textContent = 'Clear Session';

            confirmButton.onclick = function() {
                const form = document.getElementById('clear-form');
                form.action = '/admin/aliexpress/chat-sessions/' + id + '/clear';
                form.submit();
            };
        } else if (action === 'delete') {
            modalTitle.textContent = 'Confirm Delete';
            modalMessage.textContent = 'Are you sure you want to delete this chat session? This action cannot be undone.';
            modalIcon.className = 'fas fa-exclamation-triangle text-red-600';
            document.getElementById('modal-icon').className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4';
            confirmButton.className = 'bg-red-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-red-700';
            confirmButton.textContent = 'Delete Session';

            confirmButton.onclick = function() {
                const form = document.getElementById('delete-form');
                form.action = '/admin/aliexpress/chat-sessions/' + id;
                form.submit();
            };
        }

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
