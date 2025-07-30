@foreach($savedProducts as $product)
<div class="bg-gray-700 rounded-lg overflow-hidden border border-gray-600 hover:border-indigo-500 transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-indigo-900/20 saved-product-item" data-product-id="{{ $product->id }}">
    <div class="flex flex-col lg:flex-row h-full">
        <!-- Square image container with smaller size -->
        <div class="w-full lg:w-40 h-24 lg:h-40 flex-shrink-0">
            <img src="{{ $product->imgUrl }}" alt="{{ $product->title }}" class="w-full h-full object-cover transition-transform hover:scale-105 duration-500">
        </div>

        <!-- Product Details -->
        <div class="w-full p-3 flex flex-col justify-between flex-grow relative">
            <div>
                <div class="w-full">
                    <h3 class="text-sm font-medium text-white " title="{{ $product->title }}">{{ $product->title }}</h3>
                </div>
                <p class="text-indigo-400 text-sm font-bold mt-1">{{ $product->price }} JOD</p>
                <div class="flex items-center mt-1">
                    <div class="text-amber-400 text-xs flex">
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
                        <span class="text-xs text-gray-400 ml-1">({{ $product->reviews }})</span>
                    </div>
                </div>
            </div>

            <!-- View button - smaller size -->
            <div class="mt-2 pr-10">
                <a href="{{ $product->productURL }}" target="_blank" class="w-auto inline-flex text-center py-1 px-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded-md items-center justify-center">
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
@endforeach
