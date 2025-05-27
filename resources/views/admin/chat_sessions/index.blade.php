@extends('admin.layouts.admin')

@section('title', 'Chat Sessions')

@section('breadcrumbs')
    <span class="text-gray-700">Chat Sessions</span>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-semibold text-gray-800">Chat Sessions</h1>

            <!-- Filter toggle button -->
            <button id="toggle-filters" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded inline-flex items-center text-sm">
                <i class="fas fa-filter mr-2"></i> Filters
            </button>
        </div>

        <!-- Filter section -->
        <div class="mb-6 p-4 rounded-lg border border-gray-200 bg-gray-50">
            <form action="{{ route('admin.chat_sessions.index') }}" method="GET">
                <!-- Search by name - always visible -->
                <div class="mb-4">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <div class="flex">
                        <input type="text" name="search" id="search"
                            placeholder="Search by session name or user"
                            class="w-full rounded-l-md"
                            value="{{ request('search') }}">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-r-md text-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Additional filters - hidden by default -->
                <div id="advanced-filters" class="{{ request()->hasAny(['user_id', 'status', 'date_from', 'date_to', 'tag', 'min_messages', 'sort', 'per_page']) ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <!-- Filter by user -->
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">User</label>
                            <select name="user_id" id="user_id" class="w-full rounded-md">
                                <option value="">All users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter by status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" class="w-full rounded-md">
                                <option value="">All statuses</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="flagged" {{ request('status') == 'flagged' ? 'selected' : '' }}>Flagged</option>
                            </select>
                        </div>

                        <!-- Filter by date range -->
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                            <input type="date" name="date_from" id="date_from" class="w-full rounded-md" value="{{ request('date_from') }}">
                        </div>

                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                            <input type="date" name="date_to" id="date_to" class="w-full rounded-md" value="{{ request('date_to') }}">
                        </div>

                        <!-- Filter by tag -->
                        <div>
                            <label for="tag" class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                            <select name="tag" id="tag" class="w-full rounded-md">
                                <option value="">All tags</option>
                                @foreach($allTags as $tag)
                                    <option value="{{ $tag }}" {{ request('tag') == $tag ? 'selected' : '' }}>{{ $tag }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter by message count -->
                        <div>
                            <label for="min_messages" class="block text-sm font-medium text-gray-700 mb-1">Min Messages</label>
                            <input type="number" name="min_messages" id="min_messages"
                                class="w-full rounded-md"
                                value="{{ request('min_messages') }}" min="1">
                        </div>

                        <!-- Sort by -->
                        <div>
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <div class="flex">
                                <select name="sort" id="sort" class="rounded-l-md border-r-0 w-2/3">
                                    <option value="updated_at" {{ $sortField == 'updated_at' ? 'selected' : '' }}>Last Activity</option>
                                    <option value="created_at" {{ $sortField == 'created_at' ? 'selected' : '' }}>Created Date</option>
                                    <option value="name" {{ $sortField == 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="id" {{ $sortField == 'id' ? 'selected' : '' }}>ID</option>
                                    <option value="status" {{ $sortField == 'status' ? 'selected' : '' }}>Status</option>
                                </select>
                                <select name="direction" id="direction" class="rounded-r-md w-1/3">
                                    <option value="desc" {{ $sortDirection == 'desc' ? 'selected' : '' }}>Desc</option>
                                    <option value="asc" {{ $sortDirection == 'asc' ? 'selected' : '' }}>Asc</option>
                                </select>
                            </div>
                        </div>

                        <!-- Items per page -->
                        <div>
                            <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                            <select name="per_page" id="per_page" class="w-full rounded-md">
                                @foreach($allowedPerPage as $value)
                                    <option value="{{ $value }}" {{ $perPage == $value ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex space-x-2">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded text-sm">
                            <i class="fas fa-search mr-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.chat_sessions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm flex items-center">
                            <i class="fas fa-times mr-1"></i> Clear Filters
                        </a>
                    </div>
                </div>

                <!-- Toggle advanced filters button -->
                <div class="mt-2">
                    <button type="button" id="toggle-advanced-filters" class="text-sm text-gray-600 hover:text-gray-900 flex items-center">
                        <i class="fas fa-filter mr-1"></i>
                        <span id="filter-button-text">Show Advanced Filters</span>
                    </button>
                </div>
            </form>
        </div>

    <!-- Export buttons -->
    <div class="flex justify-end mb-4 space-x-2 px-6">
        <button type="button" onclick="openExportModal('csv')" class="bg-blue-600 hover:bg-blue-700 text-white py-1.5 px-3 rounded-md text-sm">
            <i class="fas fa-file-csv mr-1"></i> Export CSV
        </button>
        <button type="button" onclick="openExportModal('pdf')" class="bg-red-600 hover:bg-red-700 text-white py-1.5 px-3 rounded-md text-sm">
            <i class="fas fa-file-pdf mr-1"></i> Export PDF
        </button>
    </div>

    <!-- Sessions table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                ID / Name
                                <a href="{{ route('admin.chat_sessions.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'id', 'direction' => $sortField == 'id' && $sortDirection == 'asc' ? 'desc' : 'asc'])) }}" class="ml-1 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-sort"></i>
                                </a>
                            </div>
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                Created / Last Activity
                                <a href="{{ route('admin.chat_sessions.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'updated_at', 'direction' => $sortField == 'updated_at' && $sortDirection == 'asc' ? 'desc' : 'asc'])) }}" class="ml-1 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-sort"></i>
                                </a>
                            </div>
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Messages</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                Status
                                <a href="{{ route('admin.chat_sessions.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'status', 'direction' => $sortField == 'status' && $sortDirection == 'asc' ? 'desc' : 'asc'])) }}" class="ml-1 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-sort"></i>
                                </a>
                            </div>
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tags</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">#{{ $session->id }}</span>
                                    <span class="text-sm text-gray-700">{{ $session->name }}</span>
                                </div>
                            </td>
                            <td class="py-2 px-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">{{ $session->user->name }}</span>
                                    <span class="text-sm text-gray-700">{{ $session->user->email }}</span>
                                </div>
                            </td>
                            <td class="py-2 px-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-700">Created: {{ $session->created_at->format('Y-m-d H:i') }}</span>
                                    <span class="text-sm text-gray-700">Updated: {{ $session->updated_at->format('Y-m-d H:i') }}</span>
                                </div>
                            </td>
                            <td class="py-2 px-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $session->messages_count ?? $session->messages->count() }}</span>
                            </td>
                            <td class="py-2 px-4 whitespace-nowrap">
                                @if($session->status === 'active')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @elseif($session->status === 'closed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Closed
                                    </span>
                                @elseif($session->status === 'flagged')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Flagged
                                    </span>
                                @endif
                            </td>
                            <td class="py-2 px-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($session->tags ?? [] as $tag)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-2 px-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.chat_sessions.show', $session->id) }}"
                                       class="text-emerald-600 hover:text-emerald-900" title="View Session">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($session->status === 'active')
                                        <form action="{{ route('admin.chat_sessions.close', $session->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-600 hover:text-gray-900" title="Close Session">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        </form>
                                    @elseif($session->status === 'closed')
                                        <form action="{{ route('admin.chat_sessions.reopen', $session->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Reopen Session">
                                                <i class="fas fa-lock-open"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.chat_sessions.destroy', $session->id) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this session? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete Session">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500">
                                <p>No chat sessions found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            <div class="flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-500 mb-4 sm:mb-0">
                    Showing {{ $sessions->firstItem() ?? 0 }} to {{ $sessions->lastItem() ?? 0 }} of {{ $sessions->total() }} sessions
                </div>
                {{ $sessions->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div id="export-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center hidden">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 id="export-modal-title" class="text-lg leading-6 font-medium text-gray-900">Export Chat Sessions</h3>
                <button type="button" onclick="closeExportModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-4">
                <p class="text-sm text-gray-500 mb-4">Choose which data to export:</p>
                <form id="export-form" action="{{ route('admin.chat_sessions.export.all') }}" method="GET">
                    <input type="hidden" id="export-format" name="format" value="csv">
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input id="export-current-page" name="export_scope" type="radio" value="current_page" checked
                                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                            <label for="export-current-page" class="ml-3 block text-sm font-medium text-gray-700">
                                Current page ({{ $sessions->count() }} records)
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="export-all-pages" name="export_scope" type="radio" value="all_pages"
                                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                            <label for="export-all-pages" class="ml-3 block text-sm font-medium text-gray-700">
                                All pages ({{ $sessions->total() }} records)
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
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleFiltersButton = document.getElementById('toggle-filters');
            const filterPanel = document.querySelector('.rounded-lg.border.border-gray-200.bg-gray-50');

            const advancedFilters = document.getElementById('advanced-filters');
            const toggleAdvancedButton = document.getElementById('toggle-advanced-filters');
            const filterButtonText = document.getElementById('filter-button-text');

            // Initialize export modal
            const exportModal = document.getElementById('export-modal');
            if (exportModal) exportModal.style.display = 'none';

            // Toggle advanced filters on button click
            toggleAdvancedButton.addEventListener('click', function() {
                advancedFilters.classList.toggle('hidden');

                if (advancedFilters.classList.contains('hidden')) {
                    filterButtonText.textContent = 'Show Advanced Filters';
                } else {
                    filterButtonText.textContent = 'Hide Advanced Filters';
                }
            });

            // Check if advanced filters are visible initially and update button text
            if (!advancedFilters.classList.contains('hidden')) {
                filterButtonText.textContent = 'Hide Advanced Filters';
            }

            // Toggle filter panel on main filter button click
            toggleFiltersButton.addEventListener('click', function() {
                filterPanel.classList.toggle('hidden');
            });
        });

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

                    // Add user_id param if exists
                    if (urlParams.has('user_id')) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'user_id';
                        input.value = urlParams.get('user_id');
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

                    // Add date range params if they exist
                    if (urlParams.has('date_from')) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'date_from';
                        input.value = urlParams.get('date_from');
                        exportForm.appendChild(input);
                    }

                    if (urlParams.has('date_to')) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'date_to';
                        input.value = urlParams.get('date_to');
                        exportForm.appendChild(input);
                    }

                    // Add tag param if exists
                    if (urlParams.has('tag')) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'tag';
                        input.value = urlParams.get('tag');
                        exportForm.appendChild(input);
                    }

                    // Add min_messages param if exists
                    if (urlParams.has('min_messages')) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'min_messages';
                        input.value = urlParams.get('min_messages');
                        exportForm.appendChild(input);
                    }

                    // Add sort params if they exist
                    if (urlParams.has('sort')) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'sort';
                        input.value = urlParams.get('sort');
                        exportForm.appendChild(input);
                    }

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
    </script>
@endsection
