<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center dashboard-header">
            <h2 class="font-semibold text-xl dashboard-section-title leading-tight">
                {{ __('My AliExpress Shopping Hub') }}
            </h2>
            <a href="{{ url('/aliexpress-chatbot') }}" class="inline-flex items-center px-4 py-2 dashboard-button-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-robot mr-2"></i> Ask AliExpress Bot
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
                            <img src="{{ $product->product_main_image_url }}" alt="{{ $product->product_title }}" class="w-full h-full object-cover transition-transform hover:scale-105 duration-500" referrerpolicy="no-referrer">
                        </div>
                        <div class="p-3">
                            <h3 class="dashboard-product-title truncate">{{ $product->product_title }}</h3>
                            <p class="dashboard-product-price mt-1">${{ $product->target_sale_price }}</p>
                            <div class="flex items-center mt-1">
                                <div class="dashboard-stars flex">
                                    @php
                                        $rating = $product->evaluate_rate ?? 0;
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
                                <span class="dashboard-reviews-count ml-1">({{ $product->lastest_volume ?? 0 }})</span>
                            </div>
                            <div class="mt-2 flex justify-between space-x-1">
                                <a href="{{ $product->promotion_link }}" target="_blank" class="px-2 py-1 dashboard-button-primary flex-1 text-center flex items-center justify-center">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                                <button
                                    onclick="toggleSaveAliExpressProduct('{{ $product->product_id }}', this)"
                                    class="px-2 py-1 {{ auth()->user()->hasSavedAliExpressProduct($product->product_id) ? 'dashboard-button-saved' : 'dashboard-button-save' }} text-white text-xs rounded-md flex-1 flex items-center justify-center"
                                >
                                    <i class="{{ auth()->user()->hasSavedAliExpressProduct($product->product_id) ? 'fas' : 'far' }} fa-bookmark mr-1"></i>
                                    {{ auth()->user()->hasSavedAliExpressProduct($product->product_id) ? 'Saved' : 'Save' }}
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-6 dashboard-empty-state">
                        <p class="dashboard-muted-text">No recently viewed products. Start browsing in the AliExpress chatbot!</p>
                        <a href="{{ url('/aliexpress-chatbot') }}" class="mt-4 inline-flex items-center px-4 py-2 dashboard-button-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-robot mr-2"></i> Go to AliExpress Bot
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
                                <img src="{{ $product->product_main_image_url }}" alt="{{ $product->product_title }}" class="w-full h-full object-cover transition-transform hover:scale-105 duration-500" referrerpolicy="no-referrer">
                            </div>
                            <div class="p-3">
                                <h3 class="dashboard-product-title truncate">{{ $product->product_title }}</h3>
                                <p class="dashboard-product-price mt-1">${{ $product->target_sale_price }}</p>
                                <div class="flex items-center mt-1">
                                    <div class="dashboard-stars flex">
                                        @php
                                            $rating = $product->evaluate_rate ?? 0;
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
                                    <span class="dashboard-reviews-count ml-1">({{ $product->lastest_volume ?? 0 }})</span>
                                </div>
                                <div class="mt-2 flex flex-col space-y-1">
                                    <a href="{{ $product->promotion_link }}" target="_blank" class="px-2 py-1 dashboard-button-primary text-center flex items-center justify-center">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                    <button
                                        onclick="toggleSaveAliExpressProduct('{{ $product->product_id }}', this)"
                                        class="px-2 py-1 {{ auth()->user()->hasSavedAliExpressProduct($product->product_id) ? 'dashboard-button-saved' : 'dashboard-button-save' }} text-white text-xs rounded-md flex items-center justify-center"
                                    >
                                        <i class="{{ auth()->user()->hasSavedAliExpressProduct($product->product_id) ? 'fas' : 'far' }} fa-bookmark mr-1"></i>
                                        {{ auth()->user()->hasSavedAliExpressProduct($product->product_id) ? 'Saved' : 'Save' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="w-full dashboard-empty-state">
                            <p class="dashboard-muted-text">No recently viewed products. Start browsing in the AliExpress chatbot!</p>
                            <a href="{{ url('/aliexpress-chatbot') }}" class="mt-4 inline-flex items-center px-4 py-2 dashboard-button-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-robot mr-2"></i> Go to AliExpress Bot
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
                    <div class="dashboard-product-item shadow-md hover:shadow-lg saved-product-item" data-product-id="{{ $product->product_id }}">
                        <div class="flex flex-col lg:flex-row h-full">
                            <!-- Square image container with smaller size -->
                            <div class="w-full lg:w-40 h-24 lg:h-40 flex-shrink-0">
                                <img src="{{ $product->product_main_image_url }}" alt="{{ $product->product_title }}" class="w-full h-full object-cover transition-transform hover:scale-105 duration-500" referrerpolicy="no-referrer">
                            </div>

                            <!-- Product Details -->
                            <div class="w-full p-3 flex flex-col justify-between flex-grow relative">
                                <div>
                                    <div class="w-full">
                                        <h3 class="dashboard-product-title" title="{{ $product->product_title }}">{{ $product->product_title }}</h3>
                                    </div>
                                    <p class="dashboard-product-price mt-1">${{ $product->target_sale_price }}</p>
                                    <div class="flex items-center mt-1">
                                        <div class="dashboard-stars flex">
                                            @php
                                                $rating = $product->evaluate_rate ?? 0;
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
                                            <span class="dashboard-reviews-count ml-1">({{ $product->lastest_volume ?? 0 }})</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- View button - smaller size -->
                                <div class="mt-2 pr-10">
                                    <a href="{{ $product->promotion_link }}" target="_blank" class="w-auto inline-flex text-center py-1 px-3 dashboard-button-primary text-xs rounded-md items-center justify-center">
                                        <i class="fas fa-external-link-alt mr-1"></i> View
                                    </a>
                                </div>

                                <!-- Delete button positioned absolutely at bottom right -->
                                <button class="absolute bottom-3 right-3 w-7 h-7 flex items-center justify-center text-gray-400 hover:text-red-400 bg-gray-800 rounded-md max-w-full truncate" onclick="removeFromAliExpressSaved('{{ $product->product_id }}')" title="Remove from saved">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-2 lg:col-span-1 dashboard-empty-state">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-bookmark dashboard-empty-icon mb-3"></i>
                            <p class="dashboard-muted-text">No saved products yet. Start saving products from the AliExpress chatbot!</p>
                            <a href="{{ url('/aliexpress-chatbot') }}" class="mt-4 inline-flex items-center px-4 py-2 dashboard-button-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-robot mr-2"></i> Go to AliExpress Bot
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

        // Load more saved products functionality
        const loadMoreBtn = document.getElementById('load-more-btn');
        if (loadMoreBtn) {
            let currentPage = {{ $page }};
            loadMoreBtn.addEventListener('click', function() {
                currentPage++;

                // Show loading state
                loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Loading...';
                loadMoreBtn.disabled = true;

                // Fetch more products
                fetch(`{{ route('user.aliexpress.load.more.saved') }}?page=${currentPage}`)
                    .then(response => response.text())
                    .then(html => {
                        // Append new products to container
                        const container = document.getElementById('saved-products-container');
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;

                        while (tempDiv.firstChild) {
                            container.appendChild(tempDiv.firstChild);
                        }

                        // Update button state
                        const totalItems = {{ $totalSaved }};
                        const currentItems = currentPage * {{ $perPage }};

                        if (currentItems >= totalItems) {
                            loadMoreBtn.style.display = 'none';
                        } else {
                            loadMoreBtn.innerHTML = `<i class="fas fa-plus-circle mr-2"></i> Show More Products <span class="ml-2 text-xs">(${currentItems} of ${totalItems})</span>`;
                            loadMoreBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading more products:', error);
                        loadMoreBtn.innerHTML = '<i class="fas fa-plus-circle mr-2"></i> Show More Products';
                        loadMoreBtn.disabled = false;
                    });
            });
        }

        // Delete modal functionality
        const deleteModal = document.getElementById('delete-modal');
        const deleteModalBackdrop = document.getElementById('delete-modal-backdrop');
        const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
        const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
        let productToDelete = null;

        // Close modal when clicking backdrop or cancel
        deleteModalBackdrop.addEventListener('click', closeDeleteModal);
        cancelDeleteBtn.addEventListener('click', closeDeleteModal);

        function closeDeleteModal() {
            deleteModal.classList.add('hidden');
            productToDelete = null;
        }

        confirmDeleteBtn.addEventListener('click', function() {
            if (productToDelete) {
                removeFromAliExpressSaved(productToDelete, true);
                closeDeleteModal();
            }
        });

        // Make functions globally available
        window.removeFromAliExpressSaved = function(productId, confirmed = false) {
            if (!confirmed) {
                productToDelete = productId;
                deleteModal.classList.remove('hidden');
                return;
            }

            const productElement = document.querySelector(`[data-product-id="${productId}"]`);

            fetch(`/api/aliexpress-products/save/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.action === 'removed') {
                    if (productElement) {
                        productElement.remove();
                    }

                    // Update saved count
                    const savedCountElement = document.getElementById('saved-count');
                    if (savedCountElement) {
                        const currentCount = parseInt(savedCountElement.textContent.match(/\d+/)[0]);
                        savedCountElement.textContent = `${currentCount - 1} items`;
                    }

                    // Check if container is empty
                    const container = document.getElementById('saved-products-container');
                    if (container.children.length === 0) {
                        container.innerHTML = `
                            <div class="col-span-2 lg:col-span-1 dashboard-empty-state">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-bookmark dashboard-empty-icon mb-3"></i>
                                    <p class="dashboard-muted-text">No saved products yet. Start saving products from the AliExpress chatbot!</p>
                                    <a href="/aliexpress-chatbot" class="mt-4 inline-flex items-center px-4 py-2 dashboard-button-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <i class="fas fa-robot mr-2"></i> Go to AliExpress Bot
                                    </a>
                                </div>
                            </div>
                        `;
                    }
                }
            })
            .catch(error => {
                console.error('Error removing product:', error);
            });
        };

        window.toggleSaveAliExpressProduct = function(productId, button) {
            const icon = button.querySelector('i');
            const text = button.querySelector('span') || button.childNodes[button.childNodes.length - 1];

            // Show loading state
            button.disabled = true;
            icon.className = 'fas fa-spinner fa-spin mr-1';

            fetch(`/api/aliexpress-products/save/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.action === 'saved') {
                    button.className = button.className.replace('dashboard-button-save', 'dashboard-button-saved');
                    icon.className = 'fas fa-bookmark mr-1';
                    text.textContent = ' Saved';
                } else {
                    button.className = button.className.replace('dashboard-button-saved', 'dashboard-button-save');
                    icon.className = 'far fa-bookmark mr-1';
                    text.textContent = ' Save';
                }
                button.disabled = false;
            })
            .catch(error => {
                console.error('Error toggling save:', error);
                // Reset button state
                icon.className = 'far fa-bookmark mr-1';
                button.disabled = false;
            });
        };
    });
</script>
</x-app-layout>
