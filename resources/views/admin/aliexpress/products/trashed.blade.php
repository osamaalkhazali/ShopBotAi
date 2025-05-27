@extends('admin.layouts.admin')

@section('title', 'Trashed AliExpress Products')

@section('breadcrumbs')
    <a href="{{ route('admin.aliexpress.products.index') }}" class="text-gray-700 hover:text-emerald-600">AliExpress Products</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700">Trashed Products</span>
@endsection

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Trashed AliExpress Products</h1>
            <p class="text-gray-600">Manage deleted AliExpress products</p>
        </div>
        <a href="{{ route('admin.aliexpress.products.index') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm py-2 px-3 rounded-md inline-flex items-center transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Back to Products
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                Deleted Products ({{ $products->total() }})
            </h2>
        </div>

        @if($products->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Price
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Deleted At
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($product->product_main_image_url)
                                    <img src="{{ $product->product_main_image_url }}" alt="{{ $product->product_title }}"
                                         class="h-10 w-10 rounded-md object-cover mr-3">
                                @else
                                    <div class="h-10 w-10 rounded-md bg-gray-200 flex items-center justify-center mr-3">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($product->product_title, 50) }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $product->product_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $product->first_level_category_name ?: 'Uncategorized' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($product->target_sale_price && is_numeric($product->target_sale_price))
                                ${{ number_format((float)$product->target_sale_price, 2) }}
                            @else
                                $0.00
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->deleted_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button type="button"
                                        class="text-emerald-600 hover:text-emerald-900 inline-flex items-center"
                                        onclick="confirmRestore({{ $product->id }})">
                                    <i class="fas fa-trash-restore mr-1"></i> Restore
                                </button>

                                <button type="button"
                                        class="text-red-600 hover:text-red-900 inline-flex items-center"
                                        onclick="confirmForceDelete({{ $product->id }})">
                                    <i class="fas fa-trash-alt mr-1"></i> Delete Forever
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $products->links() }}
        </div>
        @endif
        @else
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No deleted products</h3>
            <p class="text-gray-500 mt-1">Deleted AliExpress products will appear here</p>
        </div>
        @endif
    </div>

    <!-- Confirmation Modal -->
    <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center hidden">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="text-center mb-4">
                <div id="modal-icon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i id="modal-icon-i" class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 id="modal-title" class="text-lg leading-6 font-medium text-gray-900">Confirm Action</h3>
                <div class="mt-2">
                    <p id="modal-message" class="text-sm text-gray-500">Are you sure you want to perform this action?</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="closeConfirmModal()" class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" id="confirm-button" class="bg-red-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-red-700">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden forms for different actions -->
    <form id="restore-form" method="POST" style="display: none;">
        @csrf
    </form>

    <form id="force-delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>

@section('scripts')
<script>
    // Confirmation modal functions
    function confirmRestore(id) {
        const modal = document.getElementById('confirm-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalIcon = document.getElementById('modal-icon');
        const modalIconI = document.getElementById('modal-icon-i');
        const confirmButton = document.getElementById('confirm-button');

        // Set modal content for restore
        modalTitle.textContent = 'Confirm Restore';
        modalMessage.textContent = 'Are you sure you want to restore this AliExpress product?';
        modalIcon.classList.remove('bg-red-100');
        modalIcon.classList.add('bg-green-100');
        modalIconI.classList.remove('fa-exclamation-triangle', 'text-red-600');
        modalIconI.classList.add('fa-check-circle', 'text-green-600');
        confirmButton.textContent = 'Restore';
        confirmButton.classList.remove('bg-red-600', 'hover:bg-red-700');
        confirmButton.classList.add('bg-green-600', 'hover:bg-green-700');

        // Set confirm action
        confirmButton.onclick = function() {
            const form = document.getElementById('restore-form');
            form.action = '/admin/aliexpress/products/' + id + '/restore';
            form.submit();
        };

        // Show modal
        modal.classList.add('flex');
        modal.classList.remove('hidden');
    }

    function confirmForceDelete(id) {
        const modal = document.getElementById('confirm-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalIcon = document.getElementById('modal-icon');
        const modalIconI = document.getElementById('modal-icon-i');
        const confirmButton = document.getElementById('confirm-button');

        // Set modal content for force delete
        modalTitle.textContent = 'Confirm Permanent Delete';
        modalMessage.textContent = 'Are you sure you want to permanently delete this AliExpress product? This action cannot be undone!';
        modalIcon.classList.remove('bg-green-100');
        modalIcon.classList.add('bg-red-100');
        modalIconI.classList.remove('fa-check-circle', 'text-green-600');
        modalIconI.classList.add('fa-exclamation-triangle', 'text-red-600');
        confirmButton.textContent = 'Delete Forever';
        confirmButton.classList.remove('bg-green-600', 'hover:bg-green-700');
        confirmButton.classList.add('bg-red-600', 'hover:bg-red-700');

        // Set confirm action
        confirmButton.onclick = function() {
            const form = document.getElementById('force-delete-form');
            form.action = '/admin/aliexpress/products/' + id + '/force-delete';
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
