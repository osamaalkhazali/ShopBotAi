@extends('admin.layouts.admin')

@section('title', 'Trashed Products')

@section('breadcrumbs')
    <a href="{{ route('admin.products.index') }}" class="text-gray-700 hover:text-emerald-600">Products</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700">Trashed Products</span>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Trashed Products</h2>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Products
                </a>
            </div>
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
                            Category
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Deleted
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
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $product->category->category_name ?? 'No Category' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $product->deleted_at ? $product->deleted_at->format('M d, Y h:i A') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-1">
                                    <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-900 p-1" title="Restore">
                                            <i class="fas fa-trash-restore"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.products.force-delete', $product->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 p-1" onclick="return confirm('Are you sure you want to permanently delete this product? This action cannot be undone.')" title="Delete Permanently">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-5 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-trash text-3xl mb-2 opacity-40"></i>
                                    <p>No trashed products found</p>
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
@endsection
