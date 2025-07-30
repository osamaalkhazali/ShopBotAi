@extends('admin.layouts.admin')

@section('title', 'Edit AliExpress Product')

@section('breadcrumbs')
    <a href="{{ route('admin.aliexpress.products.index') }}" class="text-gray-700 hover:text-emerald-600">AliExpress Products</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700">Edit Product</span>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Edit AliExpress Product</h2>
        </div>

        <form action="{{ route('admin.aliexpress.products.update', $product->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product ID -->
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Product ID *</label>
                    <input type="text" id="product_id" name="product_id" value="{{ old('product_id', $product->product_id) }}"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="AliExpress Product ID" required>
                    @error('product_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Title -->
                <div>
                    <label for="product_title" class="block text-sm font-medium text-gray-700 mb-1">Product Title *</label>
                    <input type="text" id="product_title" name="product_title" value="{{ old('product_title', $product->product_title) }}"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Product title" required>
                    @error('product_title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target Sale Price -->
                <div>
                    <label for="target_sale_price" class="block text-sm font-medium text-gray-700 mb-1">Target Sale Price *</label>
                    <input type="number" id="target_sale_price" name="target_sale_price" value="{{ old('target_sale_price', $product->target_sale_price) }}" step="0.01" min="0"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="0.00" required>
                    @error('target_sale_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Original Price -->
                <div>
                    <label for="original_price" class="block text-sm font-medium text-gray-700 mb-1">Original Price</label>
                    <input type="number" id="original_price" name="original_price" value="{{ old('original_price', $product->original_price) }}" step="0.01" min="0"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="0.00">
                    @error('original_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- First Level Category -->
                <div>
                    <label for="first_level_category_name" class="block text-sm font-medium text-gray-700 mb-1">First Level Category</label>
                    <input type="text" id="first_level_category_name" name="first_level_category_name" value="{{ old('first_level_category_name', $product->first_level_category_name) }}"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Main category">
                    @error('first_level_category_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Second Level Category -->
                <div>
                    <label for="second_level_category_name" class="block text-sm font-medium text-gray-700 mb-1">Second Level Category</label>
                    <input type="text" id="second_level_category_name" name="second_level_category_name" value="{{ old('second_level_category_name', $product->second_level_category_name) }}"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Subcategory">
                    @error('second_level_category_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Latest Volume -->
                <div>
                    <label for="latest_volume" class="block text-sm font-medium text-gray-700 mb-1">Latest Volume</label>
                    <input type="number" id="latest_volume" name="latest_volume" value="{{ old('latest_volume', $product->latest_volume) }}" min="0"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="0">
                    @error('latest_volume')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Commission Rate -->
                <div>
                    <label for="commission_rate" class="block text-sm font-medium text-gray-700 mb-1">Commission Rate (%)</label>
                    <input type="number" id="commission_rate" name="commission_rate" value="{{ old('commission_rate', $product->commission_rate) }}" step="0.01" min="0" max="100"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="0.00">
                    @error('commission_rate')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Shop Name -->
                <div>
                    <label for="shop_name" class="block text-sm font-medium text-gray-700 mb-1">Shop Name</label>
                    <input type="text" id="shop_name" name="shop_name" value="{{ old('shop_name', $product->shop_name) }}"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Store name">
                    @error('shop_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Shop URL -->
                <div>
                    <label for="shop_url" class="block text-sm font-medium text-gray-700 mb-1">Shop URL</label>
                    <input type="url" id="shop_url" name="shop_url" value="{{ old('shop_url', $product->shop_url) }}"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="https://aliexpress.com/store/...">
                    @error('shop_url')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Main Image URL -->
                <div class="md:col-span-2">
                    <label for="product_main_image_url" class="block text-sm font-medium text-gray-700 mb-1">Product Main Image URL</label>
                    <input type="url" id="product_main_image_url" name="product_main_image_url" value="{{ old('product_main_image_url', $product->product_main_image_url) }}"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="https://example.com/image.jpg">
                    @error('product_main_image_url')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Detail URL -->
                <div class="md:col-span-2">
                    <label for="product_detail_url" class="block text-sm font-medium text-gray-700 mb-1">Product Detail URL</label>
                    <input type="url" id="product_detail_url" name="product_detail_url" value="{{ old('product_detail_url', $product->product_detail_url) }}"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="https://aliexpress.com/item/...">
                    @error('product_detail_url')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Promotion Link -->
                <div class="md:col-span-2">
                    <label for="promotion_link" class="block text-sm font-medium text-gray-700 mb-1">Promotion Link *</label>
                    <input type="url" id="promotion_link" name="promotion_link" value="{{ old('promotion_link', $product->promotion_link) }}"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="https://aliexpress.com/..." required>
                    @error('promotion_link')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
                    <input type="url" id="url" name="url" value="{{ old('url', $product->url) }}"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="https://aliexpress.com/product/...">
                    @error('url')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Product Preview -->
            @if($product->image)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-md font-medium text-gray-700 mb-3">Current Product Image</h3>
                <img src="{{ $product->image }}" alt="{{ $product->title }}" class="w-32 h-32 object-cover rounded-md border">
            </div>
            @endif

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.aliexpress.products.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md transition-all">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white py-2 px-4 rounded-md transition-all">
                    Update Product
                </button>
            </div>
        </form>
    </div>
@endsection
