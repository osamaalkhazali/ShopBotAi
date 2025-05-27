<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductViewController extends Controller
{
    /**
     * Record a product view for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordView(Request $request, $productId)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Check if the product exists
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Record the product view
        $userId = Auth::id();
        ProductView::recordView($userId, $productId);

        return response()->json(['message' => 'View recorded successfully']);
    }

    /**
     * Get recently viewed products for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentlyViewed(Request $request)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Get the user's recently viewed products
        $recentlyViewed = Auth::user()->getRecentlyViewedProducts();

        return response()->json($recentlyViewed);
    }
}
