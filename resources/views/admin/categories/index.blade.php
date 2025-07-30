@extends('admin.layouts.admin')

@section('title', 'Categories')

@section('breadcrumbs')
    <span class="text-gray-700">Categories</span>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Categories</h2>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.categories.trashed') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all">
                        <i class="fas fa-trash-alt mr-2"></i> View Deleted
                    </a>
                    <a href="{{ route('admin.categories.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all">
                        <i class="fas fa-plus mr-2"></i> Add Category
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-grow min-w-[200px]">
                    <label for="category_name" class="block text-xs font-medium text-gray-700 mb-1">Category Name</label>
                    <input type="text" id="category_name" name="category_name" value="{{ request('category_name') }}"
                           placeholder="Search categories..." class="w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="w-full sm:w-auto">
                    <label for="sort" class="block text-xs font-medium text-gray-700 mb-1">Sort By</label>
                    <select id="sort" name="sort" class="block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="category_asc" {{ request('sort') == 'category_asc' ? 'selected' : '' }}>Category Name (A-Z)</option>
                        <option value="category_desc" {{ request('sort') == 'category_desc' ? 'selected' : '' }}>Category Name (Z-A)</option>
                        <option value="products_asc" {{ request('sort') == 'products_asc' ? 'selected' : '' }}>Products (Low to High)</option>
                        <option value="products_desc" {{ request('sort') == 'products_desc' ? 'selected' : '' }}>Products (High to Low)</option>
                        <option value="created_at_desc" {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white py-2 px-4 rounded-md">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>

                    @if(request()->hasAny(['category_name', 'sort']))
                    <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-3 rounded-md inline-flex items-center">
                        <i class="fas fa-times mr-1"></i> Clear
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Export buttons -->
        <div class="flex justify-end p-4 space-x-2">
            <button type="button" onclick="openExportModal('csv')" class="bg-blue-600 hover:bg-blue-700 text-white py-1.5 px-3 rounded-md text-sm">
                <i class="fas fa-file-csv mr-1"></i> Export CSV
            </button>
            <button type="button" onclick="openExportModal('pdf')" class="bg-red-600 hover:bg-red-700 text-white py-1.5 px-3 rounded-md text-sm">
                <i class="fas fa-file-pdf mr-1"></i> Export PDF
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            #
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'category_name_' . (request('sort') == 'category_name_asc' ? 'desc' : 'asc')]) }}" class="hover:text-gray-900">
                                Category Name
                                @if(request('sort') == 'category_name_asc')
                                    <i class="fas fa-sort-up ml-1"></i>
                                @elseif(request('sort') == 'category_name_desc')
                                    <i class="fas fa-sort-down ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'products_' . (in_array(request('sort'), ['products_asc', 'products_desc']) ? (request('sort') == 'products_asc' ? 'desc' : 'asc') : 'asc')]) }}" class="hover:text-gray-900">
                                Products
                                @if(request('sort') == 'products_asc')
                                    <i class="fas fa-sort-up ml-1"></i>
                                @elseif(request('sort') == 'products_desc')
                                    <i class="fas fa-sort-down ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at_' . (in_array(request('sort'), ['created_at_asc', 'created_at_desc']) ? (request('sort') == 'created_at_asc' ? 'desc' : 'asc') : 'asc')]) }}" class="hover:text-gray-900">
                                Created
                                @if(request('sort') == 'created_at_asc')
                                    <i class="fas fa-sort-up ml-1"></i>
                                @elseif(request('sort') == 'created_at_desc')
                                    <i class="fas fa-sort-down ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($categories as $index => $category)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ ($categories->currentPage() - 1) * $categories->perPage() + $index + 1 }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $category->category_name }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                    {{ number_format($category->products_count) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $category->created_at ? $category->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-1">
                                    <a href="{{ route('admin.categories.show', $category->id) }}" class="text-blue-600 hover:text-blue-900 p-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-emerald-600 hover:text-emerald-900 p-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 p-1" onclick="return confirm('Are you sure you want to delete this category?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-tag text-3xl mb-2 opacity-40"></i>
                                    <p>No categories found</p>
                                    <a href="{{ route('admin.categories.index') }}" class="text-blue-500 mt-1 text-sm">Clear filters</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
            {{ $categories->withQueryString()->links() }}
        </div>

        <div class="p-4 bg-gray-50 border-t border-gray-200 text-gray-500 text-sm">
            <div class="flex justify-between items-center">
                <div>
                    Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} categories
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div id="export-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center hidden">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 id="export-modal-title" class="text-lg leading-6 font-medium text-gray-900">Export Categories</h3>
                <button type="button" onclick="closeExportModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-4">
                <p class="text-sm text-gray-500 mb-4">Choose which data to export:</p>
                <form id="export-form" action="{{ route('admin.categories.export') }}" method="GET">
                    <input type="hidden" id="export-format" name="format" value="csv">
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input id="export-current-page" name="export_scope" type="radio" value="current_page" checked
                                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                            <label for="export-current-page" class="ml-3 block text-sm font-medium text-gray-700">
                                Current page ({{ $categories->count() }} records)
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="export-all-pages" name="export_scope" type="radio" value="all_pages"
                                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                            <label for="export-all-pages" class="ml-3 block text-sm font-medium text-gray-700">
                                All pages ({{ $categories->total() }} records)
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize export modal
        const exportModal = document.getElementById('export-modal');
        if (exportModal) exportModal.style.display = 'none';
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

                // Add category_name param if exists
                if (urlParams.has('category_name')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'category_name';
                    input.value = urlParams.get('category_name');
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
</script>
@endsection
@endsection
