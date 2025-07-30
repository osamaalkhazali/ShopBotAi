<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SavedProduct;
use App\Models\ProductView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    /**
     * Display the user's profile dashboard with saved products and recently viewed products.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function dashboard(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 10;

        // Get recently viewed products
        $recentlyViewed = Auth::user()->getRecentlyViewedProducts();

        // Get saved products with pagination
        $savedProductsQuery = SavedProduct::where('user_id', Auth::id())
            ->with('product')
            ->orderBy('created_at', 'desc');

        $totalSaved = $savedProductsQuery->count();

        $savedProducts = $savedProductsQuery
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($saved) {
                return $saved->product;
            });

        return view('dashboard', compact('recentlyViewed', 'savedProducts', 'page', 'perPage', 'totalSaved'));
    }

    /**
     * Load more saved products for AJAX pagination.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function loadMoreSaved(Request $request)
    {
        $page = $request->input('page', 2);  // Default to page 2 since page 1 is loaded initially
        $perPage = $request->input('per_page', 10);

        $savedProductsQuery = SavedProduct::where('user_id', Auth::id())
            ->with('product')
            ->orderBy('created_at', 'desc');

        $totalSaved = $savedProductsQuery->count();

        $savedProducts = $savedProductsQuery
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($saved) {
                return $saved->product;
            });

        $hasMore = ($page * $perPage) < $totalSaved;

        return view('partials.saved-products', compact('savedProducts', 'page', 'perPage', 'totalSaved', 'hasMore'));
    }

    /**
     * Record a product view for the authenticated user.
     *
     * @param Request $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordProductView(Request $request, $productId)
    {
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentlyViewed(Request $request)
    {
        $recentlyViewed = Auth::user()->getRecentlyViewedProducts();
        return response()->json($recentlyViewed);
    }

    /**
     * Toggle a product's saved status for the authenticated user.
     *
     * @param Request $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleSaveProduct(Request $request, $productId)
    {
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSavedProducts(Request $request)
    {
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
     * @param Request $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkIfSaved(Request $request, $productId)
    {
        // Check if the product is saved
        $userId = Auth::id();
        $saved = SavedProduct::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        return response()->json(['saved' => $saved]);
    }

    /**
     * Display the user's AliExpress dashboard with saved products and recently viewed products.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function aliexpressDashboard(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 10;

        // Get recently viewed AliExpress products
        $recentlyViewed = Auth::user()->getRecentlyViewedAliExpressProducts();

        // Get saved AliExpress products with pagination
        $savedProductsQuery = \App\Models\AliExpressSavedProduct::where('user_id', Auth::id())
            ->with('aliexpressProduct')
            ->orderBy('created_at', 'desc');

        $totalSaved = $savedProductsQuery->count();

        $savedProducts = $savedProductsQuery
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($saved) {
                return $saved->aliexpressProduct;
            });

        return view('aliexpress-dashboard', compact('recentlyViewed', 'savedProducts', 'page', 'perPage', 'totalSaved'));
    }

    /**
     * Load more saved AliExpress products for AJAX pagination.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function loadMoreAliExpressSaved(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 10;

        $savedProducts = \App\Models\AliExpressSavedProduct::where('user_id', Auth::id())
            ->with('aliexpressProduct')
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($saved) {
                return $saved->aliexpressProduct;
            });

        return view('partials.aliexpress-saved-products', compact('savedProducts'));
    }
}
