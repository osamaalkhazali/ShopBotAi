@extends('admin.layouts.admin')

@section('title', 'AliExpress Categories')

@section('breadcrumbs')
    <span class="text-gray-500">AliExpress</span>
    <span class="mx-2">/</span>
    <span class="text-gray-900">Categories</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">AliExpress Categories</h1>
            <p class="text-gray-600 text-sm mt-1">Browse official AliExpress product categories from API</p>
        </div>
    </div>

    @if(isset($error))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Error loading categories</h3>
                    <p class="mt-1 text-sm text-red-700">{{ $error }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Card -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
                <i class="fas fa-tag text-blue-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Total Categories</p>
                <p class="text-lg font-semibold text-gray-900">{{ $categories->total() ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.aliexpress.categories.index') }}">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search categories..."
                           class="w-full">
                </div>
                <button type="submit" class="filter-btn bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Search
                </button>
                <a href="{{ route('admin.aliexpress.categories.index') }}"
                   class="filter-btn bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Categories Grid -->
    @if($categories->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($categories as $category)
                <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 p-4 border border-gray-200">
                    <!-- Category Icon -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-tag text-blue-600"></i>
                        </div>
                        <button onclick="copyToClipboard('{{ $category['id'] }}')"
                                class="text-gray-400 hover:text-blue-600 transition-colors p-1 rounded hover:bg-gray-100"
                                title="Copy ID to clipboard">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>

                    <!-- Category Name -->
                    <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2 min-h-[2.5rem]">{{ $category['name'] }}</h3>

                    <!-- Category ID -->
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded text-gray-700">ID: {{ $category['id'] }}</span>
                        <button onclick="copyToClipboard('{{ $category['id'] }}')"
                                class="text-xs text-blue-600 hover:text-blue-800 transition-colors">
                            Copy
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
            <div class="bg-white rounded-lg shadow p-4">
                {{ $categories->links() }}
            </div>
        @endif
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="p-4 bg-gray-100 rounded-lg inline-block mb-4">
                <i class="fas fa-tag text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Categories Found</h3>
            <p class="text-gray-500">
                @if(isset($error))
                    Failed to load categories from API. Please try again later.
                @else
                    No AliExpress categories match your search criteria.
                @endif
            </p>
        </div>
    @endif
</div>

<!-- Toast notification for copy success -->
<div id="copy-toast" class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50">
    <i class="fas fa-check mr-2"></i>
    Category ID copied to clipboard!
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast();
    }).catch(function(err) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showToast();
    });
}

function showToast() {
    const toast = document.getElementById('copy-toast');
    toast.classList.remove('translate-x-full');
    setTimeout(() => {
        toast.classList.add('translate-x-full');
    }, 2000);
}
</script>
@endsection
