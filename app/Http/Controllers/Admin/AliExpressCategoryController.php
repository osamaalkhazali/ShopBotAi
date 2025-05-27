<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AliExpressProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AliExpressCategoryController extends Controller
{
    /**
     * Display a listing of AliExpress categories.
     */
    public function index(Request $request)
    {
        try {
            // Get categories from the API endpoint
            $categoriesResponse = AliExpressCategories();

            // Check for errors
            if (isset($categoriesResponse['error']) && $categoriesResponse['error'] === true) {
                return view('admin.aliexpress.categories.index', [
                    'categories' => collect([]),
                    'error' => $categoriesResponse['message'] ?? 'Failed to retrieve categories'
                ]);
            }

            // Extract categories from API response
            $apiCategories = [];
            if (isset($categoriesResponse['aliexpress_affiliate_category_get_response']['resp_result']['result']['categories']['category'])) {
                $rawCategories = $categoriesResponse['aliexpress_affiliate_category_get_response']['resp_result']['result']['categories']['category'];
                foreach ($rawCategories as $category) {
                    $apiCategories[] = [
                        'id' => $category['category_id'] ?? null,
                        'name' => $category['category_name'] ?? null
                    ];
                }
            }

            // Convert to collection for easier manipulation
            $categories = collect($apiCategories);

            // Apply search filter if provided
            if ($request->filled('search')) {
                $search = strtolower($request->search);
                $categories = $categories->filter(function ($category) use ($search) {
                    return str_contains(strtolower($category['name']), $search);
                });
            }

            // Sort categories by name
            $categories = $categories->sortBy('name');

            // Manually paginate the results
            $page = $request->input('page', 1);
            $perPage = 20;
            $total = $categories->count();
            $offset = ($page - 1) * $perPage;

            $paginatedCategories = $categories->slice($offset, $perPage)->values();

            // Create a simple pagination object
            $pagination = new \Illuminate\Pagination\LengthAwarePaginator(
                $paginatedCategories,
                $total,
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'pageName' => 'page',
                ]
            );

            // Append current query parameters to pagination links
            $pagination->appends($request->query());

            return view('admin.aliexpress.categories.index', ['categories' => $pagination]);
        } catch (\Exception $e) {
            Log::error('Error fetching AliExpress categories: ' . $e->getMessage());
            return view('admin.aliexpress.categories.index', [
                'categories' => collect([]),
                'error' => 'An error occurred while retrieving categories'
            ]);
        }
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.aliexpress.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:100|unique:aliexpress_products,first_level_category_name',
        ]);

        // Create a placeholder product with the new category
        AliExpressProduct::create([
            'product_id' => 'category-placeholder-' . time(),
            'product_title' => 'Category Placeholder',
            'target_sale_price' => 0,
            'first_level_category_name' => $request->category_name,
            'product_detail_url' => '#',
            'product_main_image_url' => '',
            'promotion_link' => '#',
            'sku_id' => 0,
            'app_sale_price' => 0,
            'original_price' => 0,
            'sale_price' => 0,
        ]);

        // Clear cache
        Cache::forget('aliexpress_categories_with_counts');
        Cache::forget('aliexpress_categories');

        return redirect()->route('admin.aliexpress.categories.index')
            ->with('success', 'AliExpress category created successfully.');
    }

    /**
     * Display the specified category.
     */
    public function show($categoryName)
    {
        $categoryName = urldecode($categoryName);

        $products = AliExpressProduct::where('first_level_category_name', $categoryName)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $categoryStats = [
            'total_products' => AliExpressProduct::where('first_level_category_name', $categoryName)->count(),
            'avg_price' => AliExpressProduct::where('first_level_category_name', $categoryName)->avg('target_sale_price'),
            'avg_rating' => AliExpressProduct::where('first_level_category_name', $categoryName)->avg('latest_volume'),
        ];

        return view('admin.aliexpress.categories.show', compact('categoryName', 'products', 'categoryStats'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit($categoryName)
    {
        $categoryName = urldecode($categoryName);
        return view('admin.aliexpress.categories.edit', compact('categoryName'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, $categoryName)
    {
        $categoryName = urldecode($categoryName);

        $request->validate([
            'category_name' => 'required|string|max:100|unique:aliexpress_products,first_level_category_name,' . $categoryName . ',first_level_category_name',
        ]);

        // Update all products with this category
        AliExpressProduct::where('first_level_category_name', $categoryName)
            ->update(['first_level_category_name' => $request->category_name]);

        // Clear cache
        Cache::forget('aliexpress_categories_with_counts');
        Cache::forget('aliexpress_categories');

        return redirect()->route('admin.aliexpress.categories.index')
            ->with('success', 'AliExpress category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy($categoryName)
    {
        $categoryName = urldecode($categoryName);

        // Set category to null for all products in this category
        AliExpressProduct::where('first_level_category_name', $categoryName)
            ->update(['first_level_category_name' => null]);

        // Clear cache
        Cache::forget('aliexpress_categories_with_counts');
        Cache::forget('aliexpress_categories');

        return redirect()->route('admin.aliexpress.categories.index')
            ->with('success', 'AliExpress category removed successfully. Products moved to uncategorized.');
    }

    /**
     * Export categories to CSV.
     */
    public function export(Request $request)
    {
        $query = AliExpressProduct::select('first_level_category_name')
            ->whereNotNull('first_level_category_name')
            ->groupBy('first_level_category_name');

        // Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('first_level_category_name', 'LIKE', "%{$search}%");
        }

        $categories = $query->withCount(['aliexpressSavedProducts' => function ($q) {
            $q->whereNotNull('first_level_category_name');
        }])
            ->orderBy('first_level_category_name')
            ->get();

        $filename = 'aliexpress_categories_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($categories) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Category Name',
                'Product Count',
                'Average Price',
                'Average Volume'
            ]);

            foreach ($categories as $category) {
                $avgPrice = AliExpressProduct::where('first_level_category_name', $category->first_level_category_name)->avg('target_sale_price');
                $avgVolume = AliExpressProduct::where('first_level_category_name', $category->first_level_category_name)->avg('latest_volume');

                fputcsv($file, [
                    $category->first_level_category_name,
                    $category->aliexpress_saved_products_count,
                    round($avgPrice, 2),
                    round($avgVolume, 2)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
