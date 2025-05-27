<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center dashboard-header">
            <h2 class="font-semibold text-xl dashboard-section-title leading-tight">
                {{ __('My Shopping Hub') }}
            </h2>
            <a href="{{ url('/chatbot') }}" class="inline-flex items-center px-4 py-2 dashboard-button-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-robot mr-2"></i> Ask ShopBot
            </a>
        </div>
    </x-slot>

    <div class="py-12 dashboard-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Recently Viewed Section -->
            <div class="p-4 sm:p-8 dashboard-section shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="dashboard-section-title">
                        <i class="fas fa-history mr-2 dashboard-section-title-icon"></i>
                        {{ __('Recently Viewed') }}
                    </h2>
                    <span class="dashboard-muted-text">Last updated: Today</span>
                </div>

                <!-- Large screens: Grid view with all products visible -->
                <div class="hidden lg:grid lg:grid-cols-6 gap-4">
                    @forelse($recentlyViewed as $product)
                    <div class="dashboard-product-item shadow-md hover:shadow-lg">
                        <div class="h-36 overflow-hidden">
                            <img src="{{ $product->imgUrl }}" alt="{{ $product->title }}" class="w-full h-full object-cover transition-transform hover:scale-105 duration-500">
                        </div>
                        <div class="p-3">
                            <h3 class="dashboard-product-title truncate">{{ $product->title }}</h3>
                            <p class="dashboard-product-price mt-1">{{ $product->price }} JOD</p>
                            <div class="flex items-center mt-1">
                                <div class="dashboard-stars flex">
                                    @php
                                        $rating = $product->stars;
                                        $fullStars = floor($rating);
                                        $halfStar = $rating - $fullStars >= 0.5;
                                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                    @endphp

                                    @for($i = 0; $i < $fullStars; $i++)
                                        <span title="{{ $rating }} out of 5">★</span>
                                    @endfor

                                    @if($halfStar)
                                        <span title="{{ $rating }} out of 5">★</span>
                                    @endif

                                    @for($i = 0; $i < $emptyStars; $i++)
                                        <span title="{{ $rating }} out of 5">☆</span>
                                    @endfor
                                </div>
                                <span class="dashboard-reviews-count ml-1">({{ $product->reviews }})</span>
                            </div>
                            <div class="mt-2 flex justify-between space-x-1">
                                <a href="{{ $product->productURL }}" target="_blank" class="px-2 py-1 dashboard-button-primary flex-1 text-center flex items-center justify-center">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                                <button
                                    onclick="toggleSaveProduct({{ $product->id }}, this)"
                                    class="px-2 py-1 {{ auth()->user()->hasSavedProduct($product->id) ? 'dashboard-button-saved' : 'dashboard-button-save' }} text-white text-xs rounded-md flex-1 flex items-center justify-center"
                                >
                                    <i class="{{ auth()->user()->hasSavedProduct($product->id) ? 'fas' : 'far' }} fa-bookmark mr-1"></i>
                                    {{ auth()->user()->hasSavedProduct($product->id) ? 'Saved' : 'Save' }}
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-6 dashboard-empty-state">
                        <p class="dashboard-muted-text">No recently viewed products. Start browsing in the chatbot!</p>
                        <a href="{{ url('/chatbot') }}" class="mt-4 inline-flex items-center px-4 py-2 dashboard-button-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-robot mr-2"></i> Go to Chatbot
                        </a>
                    </div>
                    @endforelse
                </div>

                <!-- Small/medium screens: Carousel with navigation arrows -->
                <div class="lg:hidden relative">
                    <div id="recently-viewed-carousel" class="flex overflow-x-auto py-4 scrollbar-none pb-2 space-x-4 scroll-smooth">
                        @forelse($recentlyViewed as $product)
                        <div class="flex-shrink-0 w-48 dashboard-product-item shadow-md hover:shadow-lg">
                            <div class="h-36 overflow-hidden">
                                <img src="{{ $product->imgUrl }}" alt="{{ $product->title }}" class="w-full h-full object-cover transition-transform hover:scale-105 duration-500">
                            </div>
                            <div class="p-3">
                                <h3 class="dashboard-product-title truncate">{{ $product->title }}</h3>
                                <p class="dashboard-product-price mt-1">{{ $product->price }} JOD</p>
                                <div class="flex items-center mt-1">
                                    <div class="dashboard-stars flex">
                                        @php
                                            $rating = $product->stars;
                                            $fullStars = floor($rating);
                                            $halfStar = $rating - $fullStars >= 0.5;
                                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                        @endphp

                                        @for($i = 0; $i < $fullStars; $i++)
                                            <span title="{{ $rating }} out of 5">★</span>
                                        @endfor

                                        @if($halfStar)
                                            <span title="{{ $rating }} out of 5">★</span>
                                        @endif

                                        @for($i = 0; $i < $emptyStars; $i++)
                                            <span title="{{ $rating }} out of 5">☆</span>
                                        @endfor
                                    </div>
                                    <span class="dashboard-reviews-count ml-1">({{ $product->reviews }})</span>
                                </div>
                                <div class="mt-2 flex flex-col space-y-1">
                                    <a href="{{ $product->productURL }}" target="_blank" class="px-2 py-1 dashboard-button-primary text-center flex items-center justify-center">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                    <button
                                        onclick="toggleSaveProduct({{ $product->id }}, this)"
                                        class="px-2 py-1 {{ auth()->user()->hasSavedProduct($product->id) ? 'dashboard-button-saved' : 'dashboard-button-save' }} text-white text-xs rounded-md flex items-center justify-center"
                                    >
                                        <i class="{{ auth()->user()->hasSavedProduct($product->id) ? 'fas' : 'far' }} fa-bookmark mr-1"></i>
                                        {{ auth()->user()->hasSavedProduct($product->id) ? 'Saved' : 'Save' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="w-full dashboard-empty-state">
                            <p class="dashboard-muted-text">No recently viewed products. Start browsing in the chatbot!</p>
                            <a href="{{ url('/chatbot') }}" class="mt-4 inline-flex items-center px-4 py-2 dashboard-button-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-robot mr-2"></i> Go to Chatbot
                            </a>
                        </div>
                        @endforelse
                    </div>

                    <!-- Navigation arrows -->
                    <button id="nav-left" class="absolute left-0 top-1/2 transform -translate-y-1/2 dashboard-nav-button p-2 rounded-r-lg focus:outline-none">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button id="nav-right" class="absolute right-0 top-1/2 transform -translate-y-1/2 dashboard-nav-button p-2 rounded-l-lg focus:outline-none">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- Saved Products Section -->
            <div class="p-4 sm:p-8 dashboard-section shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="dashboard-section-title">
                        <i class="fas fa-bookmark mr-2 dashboard-section-title-icon"></i>
                        {{ __('Saved Products') }}
                    </h2>
                    <div class="flex items-center">
                        <span class="dashboard-muted-text" id="saved-count">{{ $totalSaved }} items</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-1 gap-4" id="saved-products-container">
                    @forelse($savedProducts as $product)
                    <div class="dashboard-product-item shadow-md hover:shadow-lg saved-product-item" data-product-id="{{ $product->id }}">
                        <div class="flex flex-col lg:flex-row h-full">
                            <!-- Square image container with smaller size -->
                            <div class="w-full lg:w-40 h-24 lg:h-40 flex-shrink-0">
                                <img src="{{ $product->imgUrl }}" alt="{{ $product->title }}" class="w-full h-full object-cover transition-transform hover:scale-105 duration-500">
                            </div>

                            <!-- Product Details -->
                            <div class="w-full p-3 flex flex-col justify-between flex-grow relative">
                                <div>
                                    <div class="w-full">
                                        <h3 class="dashboard-product-title" title="{{ $product->title }}">{{ $product->title }}</h3>
                                    </div>
                                    <p class="dashboard-product-price mt-1">{{ $product->price }} JOD</p>
                                    <div class="flex items-center mt-1">
                                        <div class="dashboard-stars flex">
                                            @php
                                                $rating = $product->stars;
                                                $fullStars = floor($rating);
                                                $halfStar = $rating - $fullStars >= 0.5;
                                                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                            @endphp

                                            @for($i = 0; $i < $fullStars; $i++)
                                                <span title="{{ $rating }} out of 5">★</span>
                                            @endfor

                                            @if($halfStar)
                                                <span title="{{ $rating }} out of 5">★</span>
                                            @endif

                                            @for($i = 0; $i < $emptyStars; $i++)
                                                <span title="{{ $rating }} out of 5">☆</span>
                                            @endfor
                                            <span class="dashboard-reviews-count ml-1">({{ $product->reviews }})</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- View button - smaller size -->
                                <div class="mt-2 pr-10">
                                    <a href="{{ $product->productURL }}" target="_blank" class="w-auto inline-flex text-center py-1 px-3 dashboard-button-primary text-xs rounded-md items-center justify-center">
                                        <i class="fas fa-external-link-alt mr-1"></i> View
                                    </a>
                                </div>

                                <!-- Delete button positioned absolutely at bottom right -->
                                <button class="absolute bottom-3 right-3 w-7 h-7 flex items-center justify-center text-gray-400 hover:text-red-400 bg-gray-800 rounded-md max-w-full truncate" onclick="removeFromSaved({{ $product->id }})" title="Remove from saved">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-2 lg:col-span-1 dashboard-empty-state">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-bookmark dashboard-empty-icon mb-3"></i>
                            <p class="dashboard-muted-text">No saved products yet. Start saving products from the chatbot!</p>
                            <a href="{{ url('/chatbot') }}" class="mt-4 inline-flex items-center px-4 py-2 dashboard-button-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-robot mr-2"></i> Go to Chatbot
                            </a>
                        </div>
                    </div>
                    @endforelse
                </div>

                <!-- Show More Button instead of Pagination -->
                @if($page * $perPage < $totalSaved)
                <div class="mt-6 flex justify-center">
                    <button id="load-more-btn" class="px-4 py-2 dashboard-load-more text-sm rounded-lg flex items-center justify-center">
                        <i class="fas fa-plus-circle mr-2"></i> Show More Products
                        <span class="ml-2 text-xs">({{ $page * $perPage }} of {{ $totalSaved }})</span>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 dashboard-modal-backdrop" id="delete-modal-backdrop"></div>
    <div class="relative z-10 max-w-md mx-auto mt-32 dashboard-modal-content shadow-xl overflow-hidden">
        <div class="p-6">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle dashboard-modal-warning-icon text-3xl mb-3"></i>
                <h3 class="text-lg font-medium text-white mb-2">Remove from Saved Products</h3>
                <p class="text-gray-300 mb-6">Are you sure you want to remove this product from your saved items?</p>
            </div>
            <div class="flex justify-center space-x-4">
                <button id="cancel-delete-btn" class="px-4 py-2 dashboard-modal-cancel text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                    Cancel
                </button>
                <button id="confirm-delete-btn" class="px-4 py-2 dashboard-modal-delete text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                    Remove
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.getElementById('recently-viewed-carousel');
        const leftBtn = document.getElementById('nav-left');
        const rightBtn = document.getElementById('nav-right');

        if (carousel && leftBtn && rightBtn) {
            leftBtn.addEventListener('click', function() {
                carousel.scrollBy({ left: -200, behavior: 'smooth' });
            });

            rightBtn.addEventListener('click', function() {
                carousel.scrollBy({ left: 200, behavior: 'smooth' });
            });

            // Show/hide arrows based on scroll position
            function updateArrows() {
                // Only show left arrow if there's content to scroll to
                leftBtn.style.display = carousel.scrollLeft > 0 ? 'block' : 'none';
                // Only show right arrow if there's more content to scroll to
                rightBtn.style.display =
                    carousel.scrollLeft < carousel.scrollWidth - carousel.clientWidth ? 'block' : 'none';
            }

            carousel.addEventListener('scroll', updateArrows);
            window.addEventListener('resize', updateArrows);

            // Initial update
            updateArrows();
        }

        // Delete confirmation modal functionality
        const deleteModal = document.getElementById('delete-modal');
        const deleteModalBackdrop = document.getElementById('delete-modal-backdrop');
        const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
        const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
        let productToDelete = null;

        // Function to show delete modal
        function showDeleteModal(productId) {
            productToDelete = productId;
            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
            document.body.classList.add('overflow-hidden'); // Prevent scrolling
        }

        // Function to hide delete modal
        function hideDeleteModal() {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
            productToDelete = null;
        }

        // Close modal on backdrop click
        deleteModalBackdrop.addEventListener('click', hideDeleteModal);

        // Close modal on cancel button click
        cancelDeleteBtn.addEventListener('click', hideDeleteModal);

        // Confirm delete action
        confirmDeleteBtn.addEventListener('click', function() {
            if (productToDelete) {
                removeFromSaved(productToDelete);
            }
            hideDeleteModal();
        });

        // Modified removeFromSaved to work with modal
        window.removeFromSaved = function(productId) {
            // If called directly (without modal), show modal first
            if (productId !== productToDelete) {
                showDeleteModal(productId);
                return;
            }

            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/api/products/save/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
            .then(response => {
                if (response.ok) {
                    console.log('Product removed from saved items');
                    return response.json();
                } else {
                    console.error('Failed to remove product:', response.status);
                    return response.text().then(text => {
                        throw new Error(text);
                    });
                }
            })
            .then(data => {
                if (data.status === 'removed') {
                    // Use a toast notification instead of reloading
                    showToast('Product removed from saved items', 'success');

                    // Find and remove the product element
                    const productElement = document.querySelector(`[data-product-id="${productId}"]`);
                    if (productElement) {
                        productElement.closest('.saved-product-item').remove();

                        // Update the count
                        const countElement = document.querySelector('#saved-count');
                        if (countElement) {
                            const newCount = parseInt(countElement.textContent) - 1;
                            countElement.textContent = newCount + ' items';

                            // If no more products, show empty state
                            if (newCount === 0) {
                                const container = document.querySelector('#saved-products-container');
                                container.innerHTML = `
                                <div class="col-span-full dashboard-empty-state">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-bookmark dashboard-empty-icon mb-3"></i>
                                        <p class="dashboard-muted-text">No saved products yet. Start saving products from the chatbot!</p>
                                        <a href="{{ url('/chatbot') }}" class="mt-4 inline-flex items-center px-4 py-2 dashboard-button-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <i class="fas fa-robot mr-2"></i> Go to Chatbot
                                        </a>
                                    </div>
                                </div>`;
                            }
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error removing product from saved items. Please try again.', 'error');
            });
        };

        // Helper toast function for script section
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `dashboard-toast ${type === 'success' ? 'dashboard-toast-success' : 'dashboard-toast-error'} flex items-center space-x-2 animate-fade-in`;

            const icon = document.createElement('i');
            icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';

            const text = document.createElement('span');
            text.textContent = message;

            toast.appendChild(icon);
            toast.appendChild(text);
            document.body.appendChild(toast);

            // Add animation
            setTimeout(() => toast.classList.add('opacity-0', 'transition-opacity', 'duration-500'), 2000);
            setTimeout(() => toast.remove(), 2500);
        }

        // Show More functionality - FIXED VERSION
        const loadMoreBtn = document.getElementById('load-more-btn');
        if (loadMoreBtn) {
            let currentPage = {{ $page }};
            const perPage = {{ $perPage }};
            const totalSaved = {{ $totalSaved }};

            loadMoreBtn.addEventListener('click', function() {
                // Show loading state
                loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Loading...';
                loadMoreBtn.disabled = true;

                // Fetch next page of saved products
                currentPage++;

                fetch(`/load-more-saved?page=${currentPage}&per_page=${perPage}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'text/html',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    // Add the new HTML directly to the container
                    const container = document.getElementById('saved-products-container');

                    // Create a temporary div to hold the HTML
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;

                    // Get all new products from the temporary div
                    const newProducts = tempDiv.querySelectorAll('.saved-product-item');

                    // Append each new product to the container
                    newProducts.forEach(product => {
                        container.appendChild(product);
                    });

                    // Update button text and state
                    const loadedCount = Math.min(currentPage * perPage, totalSaved);

                    if (loadedCount < totalSaved) {
                        loadMoreBtn.innerHTML = `<i class="fas fa-plus-circle mr-2"></i> Show More Products <span class="ml-2 text-xs">(${loadedCount} of ${totalSaved})</span>`;
                        loadMoreBtn.disabled = false;
                    } else {
                        // All products loaded, hide the button
                        loadMoreBtn.parentElement.remove();
                    }

                    // Re-bind event handlers for newly added delete buttons
                    document.querySelectorAll('.saved-product-item button[onclick^="removeFromSaved"]').forEach(button => {
                        const onclickAttr = button.getAttribute('onclick');
                        const productIdMatch = onclickAttr.match(/removeFromSaved\\((\\d+)\\)/);

                        if (productIdMatch && productIdMatch[1]) {
                            const productId = productIdMatch[1];

                            button.removeAttribute('onclick');
                            button.addEventListener('click', function() {
                                removeFromSaved(parseInt(productId));
                            });
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadMoreBtn.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> Error Loading Products';
                    loadMoreBtn.disabled = false;
                    showToast('Failed to load more products. Please try again.', 'error');

                    // Reset to previous state after a delay
                    setTimeout(() => {
                        loadMoreBtn.innerHTML = `<i class="fas fa-plus-circle mr-2"></i> Show More Products <span class="ml-2 text-xs">(${(currentPage-1) * perPage} of ${totalSaved})</span>`;
                        loadMoreBtn.disabled = false;
                    }, 3000);
                });
            });
        }
    });

    // Function to toggle save status for recently viewed products
    function toggleSaveProduct(productId, button) {
        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Disable button during request
        button.disabled = true;

        fetch(`/api/products/save/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'saved') {
                button.innerHTML = '<i class="fas fa-bookmark mr-1"></i> Saved';
                button.classList.remove('dashboard-button-save');
                button.classList.add('dashboard-button-saved');
            } else {
                button.innerHTML = '<i class="far fa-bookmark mr-1"></i> Save';
                button.classList.remove('dashboard-button-saved');
                button.classList.add('dashboard-button-save');
            }

            // Show toast notification
            showToast(data.message, 'success');
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error saving product. Please try again.', 'error');
        })
        .finally(() => {
            button.disabled = false;
        });
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `dashboard-toast ${type === 'success' ? 'dashboard-toast-success' : 'dashboard-toast-error'} flex items-center space-x-2`;

        const icon = document.createElement('i');
        icon.className = type === 'success' ? 'fas fa-check-circle mr-2' : 'fas fa-exclamation-circle mr-2';

        const text = document.createElement('span');
        text.textContent = message;

        toast.appendChild(icon);
        toast.appendChild(text);
        document.body.appendChild(toast);

        // Remove toast after 3 seconds
        setTimeout(() => {
            toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }
</script>


</x-app-layout>