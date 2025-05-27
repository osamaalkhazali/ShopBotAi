@extends('admin.layouts.admin')

@section('title', 'Add New Product')

@section('breadcrumbs')
    <a href="{{ route('admin.products.index') }}" class="text-gray-700 hover:text-emerald-600">Products</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700">Add New Product</span>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Add New Product</h2>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf

            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="asin" class="block text-sm font-medium text-gray-700 mb-1">ASIN*</label>
                    <input type="text" id="asin" name="asin" value="{{ old('asin') }}" required class="w-full @error('asin') border-red-500 @enderror">
                    @error('asin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category*</label>
                    <select id="category_id" name="category_id" required class="w-full @error('category_id') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title*</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required class="w-full @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="imgUrl" class="block text-sm font-medium text-gray-700 mb-1">Image URL*</label>
                    <input type="url" id="imgUrl" name="imgUrl" value="{{ old('imgUrl') }}" required class="w-full @error('imgUrl') border-red-500 @enderror">
                    @error('imgUrl')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="productURL" class="block text-sm font-medium text-gray-700 mb-1">Product URL*</label>
                    <input type="url" id="productURL" name="productURL" value="{{ old('productURL') }}" required class="w-full @error('productURL') border-red-500 @enderror">
                    @error('productURL')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price*</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500">$</span>
                        </div>
                        <input type="number" id="price" name="price" value="{{ old('price') }}" min="0" step="0.01" required class="w-full pl-7 @error('price') border-red-500 @enderror">
                    </div>
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="listPrice" class="block text-sm font-medium text-gray-700 mb-1">List Price (Original)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500">$</span>
                        </div>
                        <input type="number" id="listPrice" name="listPrice" value="{{ old('listPrice') }}" min="0" step="0.01" class="w-full pl-7 @error('listPrice') border-red-500 @enderror">
                    </div>
                    @error('listPrice')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stars" class="block text-sm font-medium text-gray-700 mb-1">Rating (0-5)*</label>
                    <input type="number" id="stars" name="stars" value="{{ old('stars', 0) }}" min="0" max="5" step="0.1" required class="w-full @error('stars') border-red-500 @enderror">
                    @error('stars')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reviews" class="block text-sm font-medium text-gray-700 mb-1">Number of Reviews*</label>
                    <input type="number" id="reviews" name="reviews" value="{{ old('reviews', 0) }}" min="0" required class="w-full @error('reviews') border-red-500 @enderror">
                    @error('reviews')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="boughtInLastMonth" class="block text-sm font-medium text-gray-700 mb-1">Bought In Last Month</label>
                    <input type="number" id="boughtInLastMonth" name="boughtInLastMonth" value="{{ old('boughtInLastMonth', 0) }}" min="0" class="w-full @error('boughtInLastMonth') border-red-500 @enderror">
                    @error('boughtInLastMonth')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="isBestSeller" name="isBestSeller" value="1" {{ old('isBestSeller') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="isBestSeller" class="ml-2 block text-sm text-gray-700">Mark as Best Seller</label>
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 text-right sm:px-6 border-t border-gray-200">
                <a href="{{ route('admin.products.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-emerald-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-emerald-700">
                    Save Product
                </button>
            </div>
        </form>
    </div>
@endsection
