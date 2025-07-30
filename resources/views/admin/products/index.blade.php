@extends('admin.layouts.admin')

@section('title', 'Products')

@section('breadcrumbs')
    <span class="text-gray-700">Products</span>
@endsection

@section('content')
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-4 flex items-center">
                    <div class="rounded-full bg-blue-100 p-3 mr-4">
                        <i class="fas fa-box text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ number_format($products->total()) }}</h3>
                        <p class="text-sm text-gray-500">Total Products</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-4 flex items-center">
                    <div class="rounded-full bg-emerald-100 p-3 mr-4">
                        <i class="fas fa-eye text-emerald-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ number_format($viewedToday ?? 0) }}</h3>
                        <p class="text-sm text-gray-500">Viewed Today</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-4 flex items-center">
                    <div class="rounded-full bg-indigo-100 p-3 mr-4">
                        <i class="fas fa-bookmark text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ number_format($savedToday ?? 0) }}</h3>
                        <p class="text-sm text-gray-500">Saved Today</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Products</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.products.trashed') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all">
                            <i class="fas fa-trash-alt mr-2"></i> View Deleted
                        </a>
                        <a href="{{ route('admin.products.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all">
                            <i class="fas fa-plus mr-2"></i> Add Product
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="p-4 bg-gray-50">
                <form action="{{ route('admin.products.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                    <div>
                        <label for="title" class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" id="title" name="title" value="{{ request('title') }}" placeholder="Search title..."
                            class="w-full">
                    </div>

                    <div>
                        <label for="category_id" class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                        <select id="category_id" name="category_id" class="w-full">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="price_min" class="block text-xs font-medium text-gray-700 mb-1">Min Price</label>
                        <input type="number" id="price_min" name="price_min" value="{{ request('price_min') }}" min="0" step="0.01"
                            placeholder="Min price" class="w-full">
                    </div>

                    <div>
                        <label for="price_max" class="block text-xs font-medium text-gray-700 mb-1">Max Price</label>
                        <input type="number" id="price_max" name="price_max" value="{{ request('price_max') }}" min="0" step="0.01"
                            placeholder="Max price" class="w-full">
                    </div>

                    <div>
                        <label for="per_page" class="block text-xs font-medium text-gray-700 mb-1">Per Page</label>
                        <select id="per_page" name="per_page" class="w-full">
                            @foreach($allowedPerPage as $value)
                                <option value="{{ $value }}" {{ $perPage == $value ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-4 rounded-md filter-btn">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['title', 'category_id', 'price_min', 'price_max', 'sort', 'per_page']))
                            <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-sm py-2 px-3 rounded-md filter-btn">
                                <i class="fas fa-times mr-1"></i> Clear
                            </a>
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

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                #
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'title_' . (request('sort') == 'title_asc' ? 'desc' : 'asc')]) }}" class="hover:text-gray-900">
                                    Title
                                    @if(request('sort') == 'title_asc')
                                        <i class="fas fa-sort-up ml-1"></i>
                                    @elseif(request('sort') == 'title_desc')
                                        <i class="fas fa-sort-down ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 opacity-30"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Image
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_' . (request('sort') == 'price_asc' ? 'desc' : 'asc')]) }}" class="hover:text-gray-900">
                                    Price
                                    @if(request('sort') == 'price_asc')
                                        <i class="fas fa-sort-up ml-1"></i>
                                    @elseif(request('sort') == 'price_desc')
                                        <i class="fas fa-sort-down ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 opacity-30"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'stars_' . (request('sort') == 'stars_asc' ? 'desc' : 'asc')]) }}" class="hover:text-gray-900">
                                    Rating
                                    @if(request('sort') == 'stars_asc')
                                        <i class="fas fa-sort-up ml-1"></i>
                                    @elseif(request('sort') == 'stars_desc')
                                        <i class="fas fa-sort-down ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 opacity-30"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stats
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($products as $index => $product)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ ($products->currentPage() - 1) * $products->perPage() + $index + 1 }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ Str::limit($product->title, 50) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <img src="{{ $product->imgUrl }}" alt="{{ $product->title }}" class="h-10 w-10 object-cover rounded-md">
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">${{ number_format($product->price, 2) }}</div>
                                    @if($product->listPrice > $product->price)
                                        <div class="text-xs text-gray-500 line-through">${{ number_format($product->listPrice, 2) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-yellow-500">
                                            <span class="mr-1">{{ number_format($product->stars, 1) }}</span>
                                            {{-- Using a simplified star rating display for performance --}}
                                            @if($product->stars >= 4.5)
                                                <i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i>
                                            @elseif($product->stars >= 3.5)
                                                <i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="far fa-star text-xs"></i>
                                            @elseif($product->stars >= 2.5)
                                                <i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="far fa-star text-xs"></i><i class="far fa-star text-xs"></i>
                                            @elseif($product->stars >= 1.5)
                                                <i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="far fa-star text-xs"></i><i class="far fa-star text-xs"></i><i class="far fa-star text-xs"></i>
                                            @elseif($product->stars >= 0.5)
                                                <i class="fas fa-star text-xs"></i><i class="far fa-star text-xs"></i><i class="far fa-star text-xs"></i><i class="far fa-star text-xs"></i><i class="far fa-star text-xs"></i>
                                            @else
                                                <i class="far fa-star text-xs"></i><i class="far fa-star text-xs"></i><i class="far fa-star text-xs"></i><i class="far fa-star text-xs"></i><i class="far fa-star text-xs"></i>
                                            @endif
                                        </div>
                                        <span class="ml-1 text-xs text-gray-500">({{ number_format($product->reviews) }})</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->category->category_name ?? 'No Category' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex flex-col space-y-1">
                                        <div class="text-xs">
                                            <i class="fas fa-eye text-blue-500 mr-1"></i>
                                            <span title="Views today">{{ $product->todayViews ?? 0 }}</span>
                                        </div>
                                        <div class="text-xs">
                                            <i class="fas fa-bookmark text-indigo-500 mr-1"></i>
                                            <span title="Saves today">{{ $product->todaySaves ?? 0 }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-1">
                                        <a href="{{ $product->productURL }}" target="_blank" class="text-blue-500 hover:text-blue-700 p-1" title="Visit Product Page">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <a href="{{ route('admin.products.show', $product->id) }}" class="text-blue-600 hover:text-blue-900 p-1" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="text-emerald-600 hover:text-emerald-900 p-1" title="Edit Product">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                            onclick="toggleBestSeller({{ $product->id }})"
                                            class="{{ $product->isBestSeller ? 'text-yellow-500' : 'text-gray-400' }} hover:text-yellow-600 p-1"
                                            title="{{ $product->isBestSeller ? 'Remove from Best Sellers' : 'Mark as Best Seller' }}">
                                            <i class="fas fa-medal"></i>
                                        </button>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 p-1" onclick="return confirm('Are you sure you want to delete this product?')" title="Delete Product">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-5 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-box-open text-3xl mb-2 opacity-40"></i>
                                        <p>No products found</p>
                                        <a href="{{ route('admin.products.index') }}" class="text-blue-500 mt-1 text-sm">Clear filters</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                {{ $products->withQueryString()->links() }}
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-200 text-gray-500 text-sm">
                <div class="flex justify-between items-center">
                    <div>
                        Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ number_format($products->total()) }} products
                    </div>
                    <div>
                        Last updated: {{ now()->format('M d, Y h:i A') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Modal -->
        <div id="export-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center hidden">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="export-modal-title" class="text-lg leading-6 font-medium text-gray-900">Export Products</h3>
                    <button type="button" onclick="closeExportModal()" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-500 mb-4">Choose which data to export:</p>
                    <form id="export-form" action="{{ route('admin.products.export') }}" method="GET">
                        <input type="hidden" id="export-format" name="format" value="csv">
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input id="export-current-page" name="export_scope" type="radio" value="current_page" checked
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                <label for="export-current-page" class="ml-3 block text-sm font-medium text-gray-700">
                                    Current page ({{ $products->count() }} records)
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="export-all-pages" name="export_scope" type="radio" value="all_pages"
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                <label for="export-all-pages" class="ml-3 block text-sm font-medium text-gray-700">
                                    All pages ({{ $products->total() }} records)
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
            // Simplified initialization - don't use select2 for better performance with large datasets

            // Initialize export modal
            const exportModal = document.getElementById('export-modal');
            if (exportModal) exportModal.style.display = 'none';
        });

        // Function to toggle best seller status
        function toggleBestSeller(productId) {
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/admin/products/${productId}/toggle-best-seller`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({ productId: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Use DOM manipulation to update the UI without refreshing the page
                    const button = event.target.closest('button');
                    if (data.isBestSeller) {
                        button.classList.add('text-yellow-500');
                        button.classList.remove('text-gray-400');
                        button.title = 'Remove from Best Sellers';
                    } else {
                        button.classList.remove('text-yellow-500');
                        button.classList.add('text-gray-400');
                        button.title = 'Mark as Best Seller';
                    }
                } else {
                    alert('Error updating best seller status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating best seller status');
            });
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

                    // Add title param if exists
                    if (urlParams.has('title')) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'search';
                        input.value = urlParams.get('title');
                        exportForm.appendChild(input);
                    }

                    // Add category_id param if exists
                    if (urlParams.has('category_id')) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'category';
                        input.value = urlParams.get('category_id');
                        exportForm.appendChild(input);
                    }

                    // Add price params if they exist
                    if (urlParams.has('price_min')) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'min_price';
                        input.value = urlParams.get('price_min');
                        exportForm.appendChild(input);
                    }

                    if (urlParams.has('price_max')) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'max_price';
                        input.value = urlParams.get('price_max');
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
