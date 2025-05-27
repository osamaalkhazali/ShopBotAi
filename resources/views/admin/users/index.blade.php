@extends('admin.layouts.admin')

@section('title', 'Manage Users')

@section('breadcrumbs')
<span class="text-gray-700">Manage Users</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">User Management</h1>
            <p class="text-gray-600 mt-1">Manage registered users in the system.</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.users') }}?status=deleted" class="bg-gray-500 hover:bg-gray-600 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all">
                <i class="fas fa-trash-alt mr-2"></i> View Deleted
            </a>
            <a href="{{ route('admin.users.create') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all">
                <i class="fas fa-plus mr-2"></i>
                Add New User
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form action="{{ route('admin.users') }}" method="GET" class="space-y-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex-grow min-w-[240px]">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Users</label>
                    <div class="relative">
                        <input type="text" name="search" id="search"
                               placeholder="Search by name or email..."
                               value="{{ request('search') }}"
                               class="block w-full pr-10 border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <div class="w-full sm:w-auto">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status"
                            class="block w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        <option value="">All Users</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    </select>
                </div>

                <div class="w-full sm:w-auto">
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort" id="sort"
                            class="block w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Registration Date</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email</option>
                    </select>
                </div>

                <div class="w-full sm:w-auto">
                    <label for="direction" class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
                    <select name="direction" id="direction"
                            class="block w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>

                <div class="w-full sm:w-auto flex items-end">
                    <button type="submit" class="filter-btn bg-emerald-600 hover:bg-emerald-700 text-white py-2 px-4 rounded-md flex items-center">
                        <i class="fas fa-filter mr-2"></i>
                        Apply Filters
                    </button>
                </div>

                @if(request()->anyFilled(['search', 'status', 'sort', 'direction']))
                <div class="w-full sm:w-auto flex items-end">
                    <a href="{{ route('admin.users') }}" class="filter-btn text-gray-600 hover:text-emerald-700 flex items-center py-2 px-4">
                        <i class="fas fa-times-circle mr-2"></i>
                        Clear Filters
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Success message -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Export buttons -->
    <div class="flex justify-end mb-4 space-x-2">
        <button type="button" onclick="openExportModal('csv')" class="bg-blue-600 hover:bg-blue-700 text-white py-1.5 px-3 rounded-md text-sm">
            <i class="fas fa-file-csv mr-1"></i> Export CSV
        </button>
        <button type="button" onclick="openExportModal('pdf')" class="bg-red-600 hover:bg-red-700 text-white py-1.5 px-3 rounded-md text-sm">
            <i class="fas fa-file-pdf mr-1"></i> Export PDF
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            #
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="hover:text-gray-900">
                                User
                                @if(request('sort') == 'name' && request('direction') == 'asc')
                                    <i class="fas fa-sort-up ml-1"></i>
                                @elseif(request('sort') == 'name' && request('direction') == 'desc')
                                    <i class="fas fa-sort-down ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'direction' => request('sort') == 'email' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="hover:text-gray-900">
                                Email
                                @if(request('sort') == 'email' && request('direction') == 'asc')
                                    <i class="fas fa-sort-up ml-1"></i>
                                @elseif(request('sort') == 'email' && request('direction') == 'desc')
                                    <i class="fas fa-sort-down ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('sort') == 'created_at' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="hover:text-gray-900">
                                Registration Date
                                @if(request('sort') == 'created_at' && request('direction') == 'asc')
                                    <i class="fas fa-sort-up ml-1"></i>
                                @elseif(request('sort') == 'created_at' && request('direction') == 'desc' || (!request('sort') && !request('direction')))
                                    <i class="fas fa-sort-down ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $index => $user)
                    <tr class="{{ $user->deleted_at ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                        <span class="text-emerald-800 font-medium">{{ substr($user->name, 0, 2) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->deleted_at)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Deleted
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                @if(!$user->deleted_at)
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="text-emerald-600 hover:text-emerald-900"
                                   title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <button onclick="confirmDelete('{{ $user->id }}')"
                                        class="text-red-600 hover:text-red-900"
                                        title="Delete User">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @else
                                <button onclick="confirmRestore('{{ $user->id }}')"
                                        class="text-emerald-600 hover:text-emerald-900"
                                        title="Restore User">
                                    <i class="fas fa-trash-restore"></i>
                                </button>

                                <button onclick="confirmForceDelete('{{ $user->id }}')"
                                        class="text-red-600 hover:text-red-900"
                                        title="Permanently Delete User">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-users text-3xl mb-2 opacity-40"></i>
                                <p>No users found.</p>
                                @if(request()->anyFilled(['search', 'status', 'sort', 'direction']))
                                <a href="{{ route('admin.users') }}" class="text-blue-500 mt-1 text-sm">Clear filters</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="border-t border-gray-200 px-4 py-4 sm:px-6">
            {{ $users->withQueryString()->links() }}
        </div>

        <div class="p-4 bg-gray-50 border-t border-gray-200 text-gray-500 text-sm">
            <div class="flex justify-between items-center">
                <div>
                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50  items-center justify-center hidden">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="text-center mb-4">
                <div id="modal-icon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i id="modal-icon-i" class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 id="modal-title" class="text-lg leading-6 font-medium text-gray-900">Confirm Delete</h3>
                <div class="mt-2">
                    <p id="modal-message" class="text-sm text-gray-500">Are you sure you want to delete this user? This action can be undone later.</p>
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

    <!-- Hidden forms for different actions -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <form id="restore-form" method="POST" style="display: none;">
        @csrf
    </form>

    <form id="force-delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Export Modal -->
    <div id="export-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center hidden">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 id="export-modal-title" class="text-lg leading-6 font-medium text-gray-900">Export Data</h3>
                <button type="button" onclick="closeExportModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-4">
                <p class="text-sm text-gray-500 mb-4">Choose which data to export:</p>
                <form id="export-form" action="{{ route('admin.users.export') }}" method="GET">
                    <input type="hidden" id="export-format" name="format" value="csv">
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input id="export-current-page" name="export_scope" type="radio" value="current_page" checked
                                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                            <label for="export-current-page" class="ml-3 block text-sm font-medium text-gray-700">
                                Current page ({{ $users->count() }} records)
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="export-all-pages" name="export_scope" type="radio" value="all_pages"
                                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                            <label for="export-all-pages" class="ml-3 block text-sm font-medium text-gray-700">
                                All pages ({{ $users->total() }} records)
                            </label>
                        </div>

                        <!-- Keep current filters -->
                        <div class="flex items-center mt-4">
                            <input id="export-with-filters" name="with_filters" type="checkbox" checked
                                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                            <label for="export-with-filters" class="ml-3 block text-sm font-medium text-gray-700">
                                Apply current filters to export
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="closeExportModal()"
                        class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" id="confirm-export-button"
                        class="bg-emerald-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-emerald-700">
                    Export
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Confirmation modal functions
    function confirmDelete(id) {
        const modal = document.getElementById('confirm-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalIcon = document.getElementById('modal-icon');
        const modalIconI = document.getElementById('modal-icon-i');
        const confirmButton = document.getElementById('confirm-button');

        // Set modal content
        modalTitle.textContent = 'Confirm Delete';
        modalMessage.textContent = 'Are you sure you want to delete this user? This action can be undone later.';
        modalIcon.classList.remove('bg-green-100');
        modalIcon.classList.add('bg-red-100');
        modalIconI.classList.remove('fa-check-circle', 'text-green-600');
        modalIconI.classList.add('fa-exclamation-triangle', 'text-red-600');
        confirmButton.textContent = 'Delete';
        confirmButton.classList.remove('bg-green-600', 'hover:bg-green-700');
        confirmButton.classList.add('bg-red-600', 'hover:bg-red-700');

        // Set confirm action
        confirmButton.onclick = function() {
            const form = document.getElementById('delete-form');
            form.action = '/admin/users/' + id;
            form.submit();
        };

        // Show modal - use style.display instead of classList
        modal.style.display = 'flex';
    }

    function confirmRestore(id) {
        const modal = document.getElementById('confirm-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalIcon = document.getElementById('modal-icon');
        const modalIconI = document.getElementById('modal-icon-i');
        const confirmButton = document.getElementById('confirm-button');

        // Set modal content
        modalTitle.textContent = 'Confirm Restore';
        modalMessage.textContent = 'Are you sure you want to restore this deleted user?';
        modalIcon.classList.remove('bg-red-100');
        modalIcon.classList.add('bg-green-100');
        modalIconI.classList.remove('fa-exclamation-triangle', 'text-red-600');
        modalIconI.classList.add('fa-check-circle', 'text-green-600');
        confirmButton.textContent = 'Restore';
        confirmButton.classList.remove('bg-red-600', 'hover:bg-red-700');
        confirmButton.classList.add('bg-green-600', 'hover:bg-green-700');

        // Set confirm action
        confirmButton.onclick = function() {
            const form = document.getElementById('restore-form');
            form.action = '/admin/users/' + id + '/restore';
            form.submit();
        };

        // Show modal - use style.display instead of classList
        modal.style.display = 'flex';
    }

    function confirmForceDelete(id) {
        const modal = document.getElementById('confirm-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalIcon = document.getElementById('modal-icon');
        const modalIconI = document.getElementById('modal-icon-i');
        const confirmButton = document.getElementById('confirm-button');

        // Set modal content
        modalTitle.textContent = 'Confirm Permanent Delete';
        modalMessage.textContent = 'Are you sure you want to permanently delete this user? This action cannot be undone!';
        modalIcon.classList.remove('bg-green-100');
        modalIcon.classList.add('bg-red-100');
        modalIconI.classList.remove('fa-check-circle', 'text-green-600');
        modalIconI.classList.add('fa-exclamation-triangle', 'text-red-600');
        confirmButton.textContent = 'Permanently Delete';
        confirmButton.classList.remove('bg-green-600', 'hover:bg-green-700');
        confirmButton.classList.add('bg-red-600', 'hover:bg-red-700');

        // Set confirm action
        confirmButton.onclick = function() {
            const form = document.getElementById('force-delete-form');
            form.action = '/admin/users/' + id + '/force-delete';
            form.submit();
        };

        // Show modal - use style.display instead of classList
        modal.style.display = 'flex';
    }

    function closeConfirmModal() {
        // Hide modal - use style.display instead of classList
        document.getElementById('confirm-modal').style.display = 'none';
    }

    // Export modal functions
    function openExportModal(format) {
        const modal = document.getElementById('export-modal');
        const exportForm = document.getElementById('export-form');
        const formatInput = document.getElementById('export-format');
        const exportButton = document.getElementById('confirm-export-button');

        // Set format (csv or pdf)
        formatInput.value = format;

        // Update button color based on format
        if (format === 'csv') {
            exportButton.className = 'bg-blue-600 hover:bg-blue-700 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white';
        } else {
            exportButton.className = 'bg-red-600 hover:bg-red-700 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white';
        }

        // Set confirm action
        exportButton.onclick = function() {
            // Add current filters to form if checkbox is checked
            if (document.getElementById('export-with-filters').checked) {
                // Get current URL search params
                const urlParams = new URLSearchParams(window.location.search);

                // Add search param if exists
                if (urlParams.has('search')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'search';
                    input.value = urlParams.get('search');
                    exportForm.appendChild(input);
                }

                // Add status param if exists
                if (urlParams.has('status')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'status';
                    input.value = urlParams.get('status');
                    exportForm.appendChild(input);
                }

                // Add sort param if exists
                if (urlParams.has('sort')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'sort';
                    input.value = urlParams.get('sort');
                    exportForm.appendChild(input);
                }

                // Add direction param if exists
                if (urlParams.has('direction')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'direction';
                    input.value = urlParams.get('direction');
                    exportForm.appendChild(input);
                }
            }

            exportForm.submit();
        };

        // Show modal
        modal.style.display = 'flex';
    }

    function closeExportModal() {
        document.getElementById('export-modal').style.display = 'none';
    }

    // Initialize modals when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const confirmModal = document.getElementById('confirm-modal');
        const exportModal = document.getElementById('export-modal');
        if (confirmModal) confirmModal.style.display = 'none';
        if (exportModal) exportModal.style.display = 'none';
    });
</script>
@endsection
@endsection
