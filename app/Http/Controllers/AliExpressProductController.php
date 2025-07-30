<?php

namespace App\Http\Controllers;

use App\Models\AliExpressProduct;
use App\Models\AliExpressViewedProduct;
use App\Models\AliExpressSavedProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AliExpressProductController extends Controller
{
    /**
     * Record a product view for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $aliexpressProductId
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordView(Request $request, $aliexpressProductId)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Find or create the AliExpress product in our database
        $product = $this->findOrCreateProduct($request, $aliexpressProductId);

        if (!$product) {
            return response()->json(['message' => 'Unable to create or find product'], 404);
        }

        // Record the view
        $view = AliExpressViewedProduct::recordView(Auth::id(), $product->id);

        return response()->json([
            'message' => 'Product view recorded successfully',
            'view_id' => $view->id,
            'product_id' => $product->id
        ]);
    }

    /**
     * Toggle save status for an AliExpress product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $aliexpressProductId
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleSave(Request $request, $aliexpressProductId)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Find or create the AliExpress product in our database
        $product = $this->findOrCreateProduct($request, $aliexpressProductId);

        if (!$product) {
            return response()->json(['message' => 'Unable to create or find product'], 404);
        }

        // Toggle save status
        $result = AliExpressSavedProduct::toggleSave(Auth::id(), $product->id);

        return response()->json($result);
    }

    /**
     * Get saved AliExpress products for the authenticated user.
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

        // Get the user's saved AliExpress products with pagination
        $savedProducts = AliExpressSavedProduct::where('user_id', Auth::id())
            ->with('aliexpressProduct')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($savedProducts);
    }

    /**
     * Get viewed AliExpress products for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getViewedProducts(Request $request)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Get page number and size from request, with defaults
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        // Get the user's viewed AliExpress products with pagination
        $viewedProducts = AliExpressViewedProduct::where('user_id', Auth::id())
            ->with('aliexpressProduct')
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($viewedProducts);
    }

    /**
     * Check if an AliExpress product is saved by the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $aliexpressProductId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkIfSaved(Request $request, $aliexpressProductId)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['saved' => false], 200);
        }

        // Find the product by product_id first
        $product = AliExpressProduct::where('product_id', $aliexpressProductId)->first();

        if (!$product) {
            // If product doesn't exist in database yet, it definitely isn't saved
            return response()->json(['saved' => false], 200);
        }

        // Check if the product is saved
        $saved = AliExpressSavedProduct::where('user_id', Auth::id())
            ->where('aliexpress_product_id', $product->id)
            ->exists();

        return response()->json(['saved' => $saved]);
    }

    /**
     * Get most recommended AliExpress products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMostRecommended(Request $request)
    {
        $limit = $request->input('limit', 20);

        $products = AliExpressProduct::mostRecommended($limit)->get();

        return response()->json($products);
    }

    /**
     * Get AliExpress products by category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByCategory(Request $request)
    {
        $categoryId = $request->input('category_id');
        $level = $request->input('level', 'first'); // first or second
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 20);

        if (!$categoryId) {
            return response()->json(['message' => 'Category ID is required'], 400);
        }

        $products = AliExpressProduct::byCategory($categoryId, $level)
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($products);
    }

    /**
     * Search AliExpress products by price range.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByPriceRange(Request $request)
    {
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 20);

        $products = AliExpressProduct::inPriceRange($minPrice, $maxPrice)
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($products);
    }

    /**
     * Find or create an AliExpress product in our database
     */
    private function findOrCreateProduct(Request $request, $externalId)
    {
        // First try to find by product_id (which is the external id)
        $product = AliExpressProduct::where('product_id', $externalId)->first();

        if ($product) {
            return $product;
        }

        // If not found and we have product data in the request, create it
        if ($request->has('product_data')) {
            $productData = $request->input('product_data');

            $product = AliExpressProduct::create([
                'product_id' => $externalId,
                'product_title' => $productData['title'] ?? 'Unknown Product',
                'target_sale_price' => $productData['price'] ?? '0.00',
                'target_original_price' => $productData['original_price'] ?? $productData['price'] ?? '0.00',
                'target_sale_price_currency' => $productData['currency'] ?? 'USD',
                'product_main_image_url' => $productData['image_url'] ?? '',
                'product_detail_url' => $productData['product_url'] ?? '',
                'promotion_link' => $productData['product_url'] ?? '',
                'first_level_category_name' => $productData['category'] ?? 'General',
                'recommendation_count' => 0
            ]);

            return $product;
        }

        // If no product data provided, create a minimal entry
        $product = AliExpressProduct::create([
            'product_id' => $externalId,
            'product_title' => 'Product ' . $externalId,
            'target_sale_price' => '0.00',
            'target_original_price' => '0.00',
            'target_sale_price_currency' => 'USD',
            'product_main_image_url' => '',
            'product_detail_url' => '',
            'promotion_link' => '',
            'first_level_category_name' => 'General',
            'recommendation_count' => 0
        ]);

        return $product;
    }
}
