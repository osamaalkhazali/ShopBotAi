@extends('admin.layouts.admin')

@section('title', 'View Product')

@section('breadcrumbs')
    <a href="{{ route('admin.products.index') }}" class="text-gray-700 hover:text-emerald-600">Products</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700">View Product</span>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Product Details</h2>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all" onclick="return confirm('Are you sure you want to delete this product?')">
                            <i class="fas fa-trash-alt mr-2"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <div class="mb-6 text-center">
                        <img src="{{ $product->imgUrl }}" alt="{{ $product->title }}" class="h-60 w-60 object-contain mx-auto mb-4 rounded-md border border-gray-200">

                        <div class="flex justify-center">
                            <a href="{{ $product->productURL }}" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center text-sm">
                                <i class="fas fa-external-link-alt mr-1"></i> View on Amazon
                            </a>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-md p-4 mb-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Pricing Information</h3>

                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Current Price:</span>
                            <span class="text-base font-semibold text-green-600">${{ number_format($product->price, 2) }}</span>
                        </div>

                        @if($product->listPrice > 0)
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-gray-600">List Price:</span>
                            <span class="text-sm text-gray-500 line-through">${{ number_format($product->listPrice, 2) }}</span>
                        </div>

                        @if($product->listPrice > $product->price)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Discount:</span>
                            <span class="text-sm font-medium text-red-600">
                                {{ round((($product->listPrice - $product->price) / $product->listPrice) * 100) }}% OFF
                            </span>
                        </div>
                        @endif
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-md p-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Rating Information</h3>

                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Rating:</span>
                            <div class="text-yellow-500 flex items-center">
                                <span class="mr-1">{{ number_format($product->stars, 1) }}</span>
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $product->stars)
                                        <i class="fas fa-star text-xs"></i>
                                    @elseif($i - 0.5 <= $product->stars)
                                        <i class="fas fa-star-half-alt text-xs"></i>
                                    @else
                                        <i class="far fa-star text-xs"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Reviews:</span>
                            <span class="text-sm">{{ number_format($product->reviews) }}</span>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="bg-gray-50 rounded-md p-4 mb-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Product Information</h3>

                        <div class="grid grid-cols-1 gap-3">
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-500">Title</span>
                                <span class="text-sm">{{ $product->title }}</span>
                            </div>

                            <div class="flex flex-col">
                                <span class="text-xs text-gray-500">ASIN</span>
                                <span class="text-sm">{{ $product->asin }}</span>
                            </div>

                            <div class="flex flex-col">
                                <span class="text-xs text-gray-500">Category</span>
                                <span class="text-sm">{{ $product->category->category_name ?? 'No Category' }}</span>
                            </div>

                            <div class="flex items-center">
                                <span class="text-xs text-gray-500 mr-2">Best Seller:</span>
                                @if($product->isBestSeller)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-md bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-award mr-1"></i> Yes
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-md bg-gray-100 text-gray-800">
                                        <i class="fas fa-times mr-1"></i> No
                                    </span>
                                @endif
                            </div>

                            @if($product->boughtInLastMonth)
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-500">Bought in Last Month</span>
                                <span class="text-sm">{{ number_format($product->boughtInLastMonth) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-md p-4 mb-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Timestamps</h3>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-500">Created At</span>
                                <span class="text-sm">{{ $product->created_at ? $product->created_at->format('M d, Y h:i A') : 'N/A' }}</span>
                            </div>

                            <div class="flex flex-col">
                                <span class="text-xs text-gray-500">Last Updated</span>
                                <span class="text-sm">{{ $product->updated_at ? $product->updated_at->format('M d, Y h:i A') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex justify-between">
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center text-gray-700 hover:text-blue-600 text-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to Products
            </a>
        </div>
    </div>
@endsection
