@foreach($savedProducts as $product)
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
@endforeach
