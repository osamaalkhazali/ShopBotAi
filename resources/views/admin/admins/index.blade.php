@extends('admin.layouts.admin')

@section('title', 'Manage Admins')

@section('breadcrumbs')
<a href="{{ route('admin.admins') }}" class="text-gray-700 hover:text-emerald-600">Manage Admins</a>
@endsection

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Manage Admins</h1>
        <p class="text-gray-600 mt-1">View and manage administrator accounts.</p>
    </div>
    <div class="flex space-x-2">
        <button class="tab-btn bg-gray-500 hover:bg-gray-600 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all" onclick="switchTab('deleted')">
            <i class="fas fa-trash-alt mr-2"></i> View Deleted
            @if(count($trashedAdmins) > 0)
                <span class="ml-1 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                    {{ count($trashedAdmins) }}
                </span>
            @endif
        </button>
        <a href="{{ route('admin.admins.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all">
            <i class="fas fa-plus-circle mr-2"></i>
            Add New Admin
        </a>
    </div>
</div>

<!-- Tabs -->
<div class="mb-6">
    <div class="border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px">
            <li class="mr-2">
                <button class="tab-btn active inline-block p-4 text-emerald-600 border-b-2 border-emerald-600 rounded-t-lg"
                        onclick="switchTab('active')">
                    Active Admins
                </button>
            </li>
            <li class="mr-2">
                <button class="tab-btn inline-block p-4 text-gray-500 hover:text-gray-600 border-b-2 border-transparent rounded-t-lg"
                        onclick="switchTab('deleted')">
                    Deleted Admins
                    @if(count($trashedAdmins) > 0)
                        <span class="ml-1 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                            {{ count($trashedAdmins) }}
                        </span>
                    @endif
                </button>
            </li>
        </ul>
    </div>
</div>

<!-- Filters for Active Admins -->
<div id="active-tab" class="tab-content">
    <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <form id="filter-form" action="{{ route('admin.admins') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search" name="search" placeholder="Name or email" value="{{ request('search') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div>
                <label for="role-filter" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="role-filter" name="role"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>

            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select id="sort" name="sort"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    <option value="created_asc" {{ request('sort') == 'created_asc' ? 'selected' : '' }}>Created (Oldest)</option>
                    <option value="created_desc" {{ request('sort') == 'created_desc' ? 'selected' : '' }}>Created (Newest)</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="filter-btn bg-emerald-600 hover:bg-emerald-700 text-white rounded-md py-2 px-4 mr-2">
                    <i class="fas fa-filter mr-1"></i> Apply Filters
                </button>

                @if(request()->anyFilled(['search', 'role', 'sort']))
                <a href="{{ route('admin.admins') }}" class="filter-btn bg-gray-500 hover:bg-gray-600 text-white rounded-md py-2 px-4">
                    <i class="fas fa-sync-alt mr-1"></i> Reset
                </a>
                @else
                <button type="button" onclick="resetFilters()" class="filter-btn bg-gray-500 hover:bg-gray-600 text-white rounded-md py-2 px-4">
                    <i class="fas fa-sync-alt mr-1"></i> Reset
                </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Export buttons -->
    <div class="flex justify-end mb-4 space-x-2">
        <button type="button" onclick="openExportModal('csv')" class="bg-blue-600 hover:bg-blue-700 text-white py-1.5 px-3 rounded-md text-sm">
            <i class="fas fa-file-csv mr-1"></i> Export CSV
        </button>
        <button type="button" onclick="openExportModal('pdf')" class="bg-red-600 hover:bg-red-700 text-white py-1.5 px-3 rounded-md text-sm">
            <i class="fas fa-file-pdf mr-1"></i> Export PDF
        </button>
    </div>

    <!-- Admin List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="admins-table">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name_' . (request('sort') == 'name_asc' ? 'desc' : 'asc')]) }}" class="hover:text-gray-900">
                                Name
                                @if(request('sort') == 'name_asc')
                                    <i class="fas fa-sort-up ml-1"></i>
                                @elseif(request('sort') == 'name_desc')
                                    <i class="fas fa-sort-down ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'email_' . (in_array(request('sort'), ['email_asc', 'email_desc']) ? (request('sort') == 'email_asc' ? 'desc' : 'asc') : 'asc')]) }}" class="hover:text-gray-900">
                                Email
                                @if(request('sort') == 'email_asc')
                                    <i class="fas fa-sort-up ml-1"></i>
                                @elseif(request('sort') == 'email_desc')
                                    <i class="fas fa-sort-down ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'role_' . (in_array(request('sort'), ['role_asc', 'role_desc']) ? (request('sort') == 'role_asc' ? 'desc' : 'asc') : 'asc')]) }}" class="hover:text-gray-900">
                                Role
                                @if(request('sort') == 'role_asc')
                                    <i class="fas fa-sort-up ml-1"></i>
                                @elseif(request('sort') == 'role_desc')
                                    <i class="fas fa-sort-down ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_' . (in_array(request('sort'), ['created_asc', 'created_desc']) ? (request('sort') == 'created_asc' ? 'desc' : 'asc') : 'asc')]) }}" class="hover:text-gray-900">
                                Created
                                @if(request('sort') == 'created_asc')
                                    <i class="fas fa-sort-up ml-1"></i>
                                @elseif(request('sort') == 'created_desc')
                                    <i class="fas fa-sort-down ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($admins as $index => $admin)
                    <tr class="hover:bg-gray-50 admin-row"
                        data-name="{{ strtolower($admin->name) }}"
                        data-email="{{ strtolower($admin->email) }}"
                        data-role="{{ $admin->role }}"
                        data-created="{{ $admin->created_at->timestamp }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ($admins->currentPage() - 1) * $admins->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                    <i class="fas fa-user-shield text-emerald-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $admin->name }}</div>
                                    @if(Auth::guard('admin')->id() === $admin->id)
                                    <div class="text-xs text-emerald-600">(You)</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $admin->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($admin->role === 'super_admin')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                      bg-purple-100 text-purple-800">
                                    Super Admin
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                      bg-emerald-100 text-emerald-800">
                                    Admin
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $admin->created_at ? $admin->created_at->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.admins.edit', $admin->id) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if(!(Auth::guard('admin')->id() === $admin->id ||
                                      ($admin->role === 'super_admin' && Auth::guard('admin')->user()->role !== 'super_admin')))
                                <button type="button" onclick="confirmDelete('{{ $admin->id }}')" class="text-red-600 hover:text-red-900" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Empty state when filtering returns no results -->
        <div id="no-results" class="text-center py-10 hidden">
            <div class="text-gray-400 text-5xl mb-3">
                <i class="fas fa-search"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No admins found</h3>
            <p class="text-gray-500 mt-1">Try adjusting your search or filter criteria</p>
        </div>

        @if($admins->isEmpty())
        <div class="text-center py-10">
            <div class="text-gray-400 text-5xl mb-3">
                <i class="fas fa-users-slash"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No admins found</h3>
            <p class="text-gray-500 mt-1">Start by adding a new admin</p>
            <div class="mt-4">
                <a href="{{ route('admin.admins.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm
                          font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2
                          focus:ring-offset-2 focus:ring-emerald-500">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Add New Admin
                </a>
            </div>
        </div>
        @endif

        <!-- Pagination -->
        @if($admins->isNotEmpty())
        <div class="border-t border-gray-200 px-4 py-4 sm:px-6">
            {{ $admins->withQueryString()->links() }}
        </div>
        <div class="p-4 bg-gray-50 border-t border-gray-200 text-gray-500 text-sm">
            <div class="flex justify-between items-center">
                <div>
                    Showing {{ $admins->firstItem() ?? 0 }} to {{ $admins->lastItem() ?? 0 }} of {{ $admins->total() }} admins
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Deleted Admins Tab -->
<div id="deleted-tab" class="tab-content hidden">
    <!-- Filters for deleted admins -->
    <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <form id="deleted-filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="deleted-search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="deleted-search" name="search" placeholder="Name or email"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div>
                <label for="deleted-role-filter" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="deleted-role-filter" name="role"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>

            <div>
                <label for="deleted-sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select id="deleted-sort" name="sort"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="name_asc">Name (A-Z)</option>
                    <option value="name_desc">Name (Z-A)</option>
                    <option value="deleted_asc">Deleted (Oldest)</option>
                    <option value="deleted_desc">Deleted (Newest)</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="button" onclick="applyDeletedFilters()"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md mr-2">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <button type="button" onclick="resetDeletedFilters()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-sync-alt mr-1"></i> Reset
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="deleted-admins-table">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deleted</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($trashedAdmins as $index => $admin)
                    <tr class="hover:bg-gray-50 deleted-admin-row"
                        data-name="{{ strtolower($admin->name) }}"
                        data-email="{{ strtolower($admin->email) }}"
                        data-role="{{ $admin->role }}"
                        data-deleted="{{ $admin->deleted_at->timestamp }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-user-slash text-gray-500"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $admin->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $admin->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($admin->role === 'super_admin')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                      bg-gray-100 text-gray-800">
                                    Super Admin
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                      bg-gray-100 text-gray-800">
                                    Admin
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $admin->deleted_at ? $admin->deleted_at->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button type="button" onclick="confirmRestore('{{ $admin->id }}')" class="text-green-600 hover:text-green-900" title="Restore">
                                    <i class="fas fa-trash-restore"></i>
                                </button>

                                <button type="button" onclick="confirmForceDelete('{{ $admin->id }}')" class="text-red-600 hover:text-red-900" title="Permanently Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Empty state for filtered deleted admins -->
        <div id="no-deleted-results" class="text-center py-10 hidden">
            <div class="text-gray-400 text-5xl mb-3">
                <i class="fas fa-search"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No deleted admins found</h3>
            <p class="text-gray-500 mt-1">Try adjusting your search or filter criteria</p>
        </div>

        @if($trashedAdmins->isEmpty())
        <div class="text-center py-10">
            <div class="text-gray-400 text-5xl mb-3">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No deleted admins</h3>
            <p class="text-gray-500 mt-1">Deleted admins will appear here</p>
        </div>
        @endif
    </div>
</div>

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
            <form id="export-form" action="" method="GET">
                <input type="hidden" id="export-format" name="format" value="csv">
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input id="export-current-page" name="export_scope" type="radio" value="current_page" checked
                               class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                        <label for="export-current-page" class="ml-3 block text-sm font-medium text-gray-700">
                            Current page ({{ $admins->count() }} records)
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input id="export-all-pages" name="export_scope" type="radio" value="all_pages"
                               class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                        <label for="export-all-pages" class="ml-3 block text-sm font-medium text-gray-700">
                            All pages ({{ $admins->total() }} records)
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

<!-- Notes and Tips -->
<div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-6 rounded-md">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-600"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">Administrator Management Notes</h3>
            <div class="text-sm text-blue-700 mt-1">
                <ul class="list-disc pl-5 space-y-1">
                    <li>Only Super Admins can add or manage other administrators.</li>
                    <li>Super Admins cannot be deleted by regular Admins.</li>
                    <li>You cannot delete your own account.</li>
                    <li>Deleted admins can be restored or permanently deleted from the "Deleted Admins" tab.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50  items-center justify-center hidden">
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
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="restore-form" method="POST" style="display: none;">
    @csrf
</form>

@section('scripts')
<script>
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
            tab.classList.remove('flex');
        });

        // Show selected tab content
        const activeTab = document.getElementById(tabName + '-tab');
        activeTab.classList.remove('hidden');
        activeTab.classList.add('block');

        // Update tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'text-emerald-600', 'border-emerald-600');
            btn.classList.add('text-gray-500', 'border-transparent');
        });

        // Activate clicked tab button
        event.currentTarget.classList.remove('text-gray-500', 'border-transparent');
        event.currentTarget.classList.add('active', 'text-emerald-600', 'border-emerald-600');
    }

    // Filter functions for active admins
    function applyFilters() {
        const search = document.getElementById('search').value.toLowerCase();
        const roleFilter = document.getElementById('role-filter').value;
        const sort = document.getElementById('sort').value;
        const rows = document.querySelectorAll('.admin-row');
        let visibleCount = 0;

        // First sort the rows
        const tbody = document.querySelector('#admins-table tbody');
        const rowsArray = Array.from(rows);

        if (sort === 'name_asc') {
            rowsArray.sort((a, b) => a.getAttribute('data-name').localeCompare(b.getAttribute('data-name')));
        } else if (sort === 'name_desc') {
            rowsArray.sort((a, b) => b.getAttribute('data-name').localeCompare(a.getAttribute('data-name')));
        } else if (sort === 'created_asc') {
            rowsArray.sort((a, b) => parseInt(a.getAttribute('data-created')) - parseInt(b.getAttribute('data-created')));
        } else if (sort === 'created_desc') {
            rowsArray.sort((a, b) => parseInt(b.getAttribute('data-created')) - parseInt(a.getAttribute('data-created')));
        }

        // Clear tbody and append sorted rows
        tbody.innerHTML = '';
        rowsArray.forEach(row => tbody.appendChild(row));

        // Then filter the rows
        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const email = row.getAttribute('data-email');
            const role = row.getAttribute('data-role');

            if ((search === '' || name.includes(search) || email.includes(search)) &&
                (roleFilter === '' || role === roleFilter)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide no results message
        const noResults = document.getElementById('no-results');
        if (visibleCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }

    function resetFilters() {
        document.getElementById('search').value = '';
        document.getElementById('role-filter').value = '';
        document.getElementById('sort').value = 'name_asc';

        document.querySelectorAll('.admin-row').forEach(row => {
            row.style.display = '';
        });

        document.getElementById('no-results').style.display = 'none';
    }

    // Filter functions for deleted admins
    function applyDeletedFilters() {
        const search = document.getElementById('deleted-search').value.toLowerCase();
        const roleFilter = document.getElementById('deleted-role-filter').value;
        const sort = document.getElementById('deleted-sort').value;
        const rows = document.querySelectorAll('.deleted-admin-row');
        let visibleCount = 0;

        // Sort the rows
        const tbody = document.querySelector('#deleted-admins-table tbody');
        const rowsArray = Array.from(rows);

        if (sort === 'name_asc') {
            rowsArray.sort((a, b) => a.getAttribute('data-name').localeCompare(b.getAttribute('data-name')));
        } else if (sort === 'name_desc') {
            rowsArray.sort((a, b) => b.getAttribute('data-name').localeCompare(a.getAttribute('data-name')));
        } else if (sort === 'deleted_asc') {
            rowsArray.sort((a, b) => parseInt(a.getAttribute('data-deleted')) - parseInt(b.getAttribute('data-deleted')));
        } else if (sort === 'deleted_desc') {
            rowsArray.sort((a, b) => parseInt(b.getAttribute('data-deleted')) - parseInt(a.getAttribute('data-deleted')));
        }

        tbody.innerHTML = '';
        rowsArray.forEach(row => tbody.appendChild(row));

        // Filter the rows
        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const email = row.getAttribute('data-email');
            const role = row.getAttribute('data-role');

            if ((search === '' || name.includes(search) || email.includes(search)) &&
                (roleFilter === '' || role === roleFilter)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide no results message
        const noResults = document.getElementById('no-deleted-results');
        if (visibleCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }

    function resetDeletedFilters() {
        document.getElementById('deleted-search').value = '';
        document.getElementById('deleted-role-filter').value = '';
        document.getElementById('deleted-sort').value = 'deleted_desc';

        document.querySelectorAll('.deleted-admin-row').forEach(row => {
            row.style.display = '';
        });

        document.getElementById('no-deleted-results').style.display = 'none';
    }

    // Confirmation modal functions
    function showConfirmModal(title, message, iconClass, buttonClass, confirmCallback) {
        const modal = document.getElementById('confirm-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalIcon = document.getElementById('modal-icon');
        const modalIconI = document.getElementById('modal-icon-i');
        const confirmButton = document.getElementById('confirm-button');

        modalTitle.textContent = title;
        modalMessage.textContent = message;

        // Set icon appearance
        modalIcon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full ' + iconClass;
        modalIconI.className = 'fas ' + (iconClass.includes('red') ? 'fa-exclamation-triangle' : 'fa-trash-restore') + ' ' + buttonClass;

        // Set button appearance
        confirmButton.className = 'border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white ' + buttonClass;

        // Set confirm action
        confirmButton.onclick = confirmCallback;

        // Show modal
        modal.style.display = 'flex';
    }

    function closeConfirmModal() {
        document.getElementById('confirm-modal').style.display = 'none';
    }

    // Delete confirmation
    function confirmDelete(id) {
        showConfirmModal(
            'Confirm Delete',
            'Are you sure you want to delete this admin? This item will be moved to the trash.',
            'bg-red-100',
            'bg-red-600 hover:bg-red-700',
            function() {
                const form = document.getElementById('delete-form');
                form.action = '/admin/admins/' + id;
                form.submit();
            }
        );
    }

    // Restore confirmation
    function confirmRestore(id) {
        showConfirmModal(
            'Confirm Restore',
            'Are you sure you want to restore this admin?',
            'bg-green-100',
            'bg-green-600 hover:bg-green-700',
            function() {
                const form = document.getElementById('restore-form');
                form.action = '/admin/admins/' + id + '/restore';
                form.submit();
            }
        );
    }

    // Force delete confirmation
    function confirmForceDelete(id) {
        showConfirmModal(
            'Confirm Permanent Delete',
            'Are you sure you want to permanently delete this admin? This action cannot be undone.',
            'bg-red-100',
            'bg-red-600 hover:bg-red-700',
            function() {
                const form = document.getElementById('delete-form');
                form.action = '/admin/admins/' + id + '/force';
                form.submit();
            }
        );
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

        // Set form action
        exportForm.action = "{{ route('admin.admins.export') }}";

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

                // Add role param if exists
                if (urlParams.has('role')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'role';
                    input.value = urlParams.get('role');
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
            }

            exportForm.submit();
        };

        // Show modal
        modal.style.display = 'flex';
    }

    function closeExportModal() {
        document.getElementById('export-modal').style.display = 'none';
    }

    // Initialize with default sorting
    document.addEventListener('DOMContentLoaded', function() {
        // Set default sort for deleted items to newest first
        document.getElementById('deleted-sort').value = 'deleted_desc';

        // Setup modal initial states
        const confirmModal = document.getElementById('confirm-modal');
        const exportModal = document.getElementById('export-modal');
        if (confirmModal) {
            confirmModal.style.display = 'none';
        }
        if (exportModal) {
            exportModal.style.display = 'none';
        }
    });
</script>
@endsection
@endsection
