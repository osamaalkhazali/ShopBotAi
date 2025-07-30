@extends('admin.layouts.admin')

@section('title', 'View Category')

@section('breadcrumbs')
    <a href="{{ route('admin.categories.index') }}" class="text-gray-700 hover:text-emerald-600">Categories</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700">{{ $category->category_name }}</span>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Category Details: {{ $category->category_name }}</h2>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all" onclick="return confirm('Are you sure you want to delete this category?')">
                            <i class="fas fa-trash-alt mr-2"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="p-4">
            <div class="grid grid-cols-1 gap-6">
                <div class="bg-gray-50 rounded-md p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Category Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-500">Category Name</span>
                            <span class="text-sm font-medium">{{ $category->category_name }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-xs text-gray-500">Created</span>
                            <span class="text-sm">{{ $category->created_at ? $category->created_at->format('M d, Y') : 'N/A' }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-xs text-gray-500">Products</span>
                            <span class="text-sm">
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                    {{ number_format($products->total()) }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-sm font-medium text-gray-700">Products in this Category</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Image
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Price
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rating
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($products as $product)
                                    <tr>
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
                                                <span class="ml-1 text-xs text-gray-500">({{ number_format($product->reviews) }})</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-1">
                                                <a href="{{ route('admin.products.show', $product->id) }}" class="text-blue-600 hover:text-blue-900 p-1">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product->id) }}" class="text-emerald-600 hover:text-emerald-900 p-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-5 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-box-open text-3xl mb-2 opacity-40"></i>
                                                <p>No products found in this category</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex justify-between">
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center text-gray-700 hover:text-blue-600 text-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to Categories
            </a>
        </div>
    </div>
@endsection
