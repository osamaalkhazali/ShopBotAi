<?php

namespace App\Http\Controllers;

use App\Models\SavedProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedProductController extends Controller
{
    /**
     * Toggle a product's saved status for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleSave(Request $request, $productId)
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

        // Toggle save status
        $userId = Auth::id();
        $result = SavedProduct::toggleSave($userId, $productId);

        return response()->json($result);
    }

    /**
     * Get saved products for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSavedProducts(Request $request)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Get page number and size from request, with defaults
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        // Get the user's saved products with pagination
        $savedProducts = SavedProduct::where('user_id', Auth::id())
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($savedProducts);
    }

    /**
     * Check if a product is saved by the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkIfSaved(Request $request, $productId)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['saved' => false], 200);
        }

        // Check if the product is saved
        $userId = Auth::id();
        $saved = SavedProduct::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        return response()->json(['saved' => $saved]);
    }
}
