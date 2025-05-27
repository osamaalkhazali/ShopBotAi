@extends('admin.layouts.admin')

@section('title', 'Edit Category')

@section('breadcrumbs')
    <a href="{{ route('admin.categories.index') }}" class="text-gray-700 hover:text-emerald-600">Categories</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700">Edit Category</span>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Edit Category</h2>
        </div>

        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-4">
                <div>
                    <label for="category_name" class="block text-sm font-medium text-gray-700 mb-1">Category Name*</label>
                    <input type="text" id="category_name" name="category_name" value="{{ old('category_name', $category->category_name) }}" required class="w-full @error('category_name') border-red-500 @enderror">
                    @error('category_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 text-right sm:px-6 border-t border-gray-200">
                <a href="{{ route('admin.categories.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-emerald-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-emerald-700">
                    Update Category
                </button>
            </div>
        </form>
    </div>
@endsection
