@extends('admin.layouts.admin')

@section('title', 'View AliExpress Product')

@section('breadcrumbs')
    <a href="{{ route('admin.aliexpress.products.index') }}" class="text-gray-700 hover:text-emerald-600">AliExpress Products</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700">View Product</span>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">AliExpress Product Details</h2>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.aliexpress.products.edit', $product->id) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    <a href="{{ route('admin.aliexpress.products.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Product Image -->
                <div class="lg:col-span-1">
                    @if($product->product_main_image_url)
                        <img src="{{ $product->product_main_image_url }}" alt="{{ $product->product_title }}"
                             class="w-full h-64 object-cover rounded-lg border shadow-sm">
                    @else
                        <div class="w-full h-64 bg-gray-100 rounded-lg border flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <i class="fas fa-image text-4xl mb-2"></i>
                                <p>No image available</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Product Details -->
                <div class="lg:col-span-2 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Product ID</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $product->product_id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Product Title</label>
                        <p class="text-lg text-gray-800">{{ $product->product_title }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Target Sale Price</label>
                            <p class="text-lg font-semibold text-emerald-600">
                                @if($product->target_sale_price && is_numeric($product->target_sale_price))
                                    ${{ number_format((float)$product->target_sale_price, 2) }}
                                @else
                                    Not specified
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Original Price</label>
                            <p class="text-lg text-gray-800">
                                @if($product->original_price && is_numeric($product->original_price))
                                    ${{ number_format((float)$product->original_price, 2) }}
                                @else
                                    Not specified
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">First Level Category</label>
                            <p class="text-lg text-gray-800">{{ $product->first_level_category_name ?: 'Not specified' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Second Level Category</label>
                            <p class="text-lg text-gray-800">{{ $product->second_level_category_name ?: 'Not specified' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Latest Volume</label>
                            <p class="text-lg text-gray-800">
                                @if($product->latest_volume && is_numeric($product->latest_volume))
                                    {{ number_format((int)$product->latest_volume) }}
                                @else
                                    0
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Commission Rate</label>
                            <p class="text-lg text-gray-800">
                                @if($product->commission_rate && is_numeric($product->commission_rate))
                                    {{ number_format((float)$product->commission_rate, 2) }}%
                                @else
                                    Not specified
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Shop Name</label>
                            <p class="text-lg text-gray-800">{{ $product->shop_name ?: 'Not specified' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Shop URL</label>
                            @if($product->shop_url)
                                <a href="{{ $product->shop_url }}" target="_blank"
                                   class="text-blue-600 hover:text-blue-800 break-all inline-flex items-center">
                                    {{ Str::limit($product->shop_url, 40) }}
                                    <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                </a>
                            @else
                                <p class="text-lg text-gray-500">Not specified</p>
                            @endif
                        </div>
                    </div>

                    @if($product->product_detail_url)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Product Detail URL</label>
                        <a href="{{ $product->product_detail_url }}" target="_blank"
                           class="text-blue-600 hover:text-blue-800 break-all inline-flex items-center">
                            {{ Str::limit($product->product_detail_url, 60) }}
                            <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                        </a>
                    </div>
                    @endif

                    @if($product->promotion_link)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Promotion Link</label>
                        <a href="{{ $product->promotion_link }}" target="_blank"
                           class="text-emerald-600 hover:text-emerald-800 break-all inline-flex items-center">
                            {{ Str::limit($product->promotion_link, 60) }}
                            <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                        </a>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Created</label>
                            <p class="text-lg text-gray-800">{{ $product->created_at->format('M d, Y H:i') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                            <p class="text-lg text-gray-800">{{ $product->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Product Statistics</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="rounded-full bg-blue-100 p-2 mr-3">
                                <i class="fas fa-eye text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Views</p>
                                <p class="text-lg font-semibold text-gray-800">{{ number_format($viewsCount) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-emerald-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="rounded-full bg-emerald-100 p-2 mr-3">
                                <i class="fas fa-bookmark text-emerald-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Saves</p>
                                <p class="text-lg font-semibold text-gray-800">{{ number_format($savesCount) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <button type="button"
                        onclick="confirmDelete({{ $product->id }})"
                        class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md transition-all">
                    <i class="fas fa-trash mr-2"></i> Delete Product
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
