@extends('admin.layouts.admin')

@section('title', 'AliExpress Products')

@section('breadcrumbs')
    <span class="text-gray-500">AliExpress</span>
    <span class="mx-2">/</span>
    <span class="text-gray-900">Products</span>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">AliExpress Products</h1>
                <p class="text-gray-600 text-sm mt-1">Manage AliExpress products in your system</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.aliexpress.products.create') }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center text-sm">
                    <i class="fas fa-plus mr-2"></i>
                    Add Product
                </a>
                <a href="{{ route('admin.aliexpress.products.export', request()->query()) }}"
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
                        <i class="fas fa-boxes text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600">Total Products</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $products->total() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-tag text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600">Categories</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $categories->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i class="fas fa-dollar-sign text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600">Avg Price</p>
                        <p class="text-lg font-semibold text-gray-900">
                            @php
    $avgPrice = $products->avg('target_sale_price') ?? 0;
                            @endphp
                            ${{ number_format((float) $avgPrice, 2) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-star text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600">Avg Volume</p>
                        <p class="text-lg font-semibold text-gray-900">
                            @php
    $avgVolume = $products->avg('latest_volume') ?? 0;
                            @endphp
                            {{ number_format((float) $avgVolume, 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4">
            <form method="GET" action="{{ route('admin.aliexpress.products.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Product title or ID..."
                               class="w-full">
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" id="category" class="w-full">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="min_price" class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
                        <input type="number"
                               name="min_price"
                               id="min_price"
                               value="{{ request('min_price') }}"
                               placeholder="0.00"
                               step="0.01"
                               min="0"
                               class="w-full">
                    </div>
                    <div>
                        <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                        <input type="number"
                               name="max_price"
                               id="max_price"
                               value="{{ request('max_price') }}"
                               placeholder="999.99"
                               step="0.01"
                               min="0"
                               class="w-full">
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="filter-btn bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('admin.aliexpress.products.index') }}"
                       class="filter-btn bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shop</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volume</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-md object-cover"
                                                 src="{{ $product->product_main_image_url }}"
                                                 alt="{{ $product->product_title }}"
                                                 onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 truncate max-w-xs">
                                                {{ Str::limit($product->product_title, 60) }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ID: {{ $product->product_id }}
                                            </div>
                                            @if($product->promotion_link)
                                                <a href="{{ $product->promotion_link }}"
                                                   target="_blank"
                                                   class="text-xs text-blue-600 hover:text-blue-800">
                                                    View on AliExpress
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $product->first_level_category_name }}</div>
                                    @if($product->second_level_category_name)
                                        <div class="text-sm text-gray-500">{{ $product->second_level_category_name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @php
        $price = $product->target_sale_price ?? $product->sale_price ?? 0;
        $originalPrice = $product->target_original_price ?? 0;
                                        @endphp
                                        ${{ number_format((float) $price, 2) }}
                                    </div>
                                    @if($originalPrice && is_numeric($originalPrice) && (float) $originalPrice > (float) $price)
                                        <div class="text-sm text-gray-500 line-through">
                                            ${{ number_format((float) $originalPrice, 2) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $product->shop_name ?: 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">
                                        Commission:
                                        @if($product->commission_rate && is_numeric($product->commission_rate))
                                            {{ number_format((float) $product->commission_rate, 2) }}%
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($product->latest_volume && is_numeric($product->latest_volume))
                                        {{ number_format((int) $product->latest_volume) }}
                                    @else
                                        0
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('admin.aliexpress.products.show', $product) }}"
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        <a href="{{ route('admin.aliexpress.products.edit', $product) }}"
                                           class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <button type="button"
                                                class="text-red-600 hover:text-red-900"
                                                onclick="confirmDelete({{ $product->id }})">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No AliExpress products found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif
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
                        <p id="modal-message" class="text-sm text-gray-500">Are you sure you want to delete this AliExpress product? This action can be undone later.</p>
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
    </div>

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
            form.action = '/admin/aliexpress/products/' + id;
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
