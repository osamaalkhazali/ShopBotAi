<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AliExpressProduct;
use App\Models\AliExpressSavedProduct;
use App\Models\AliExpressViewedProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AliExpressProductController extends Controller
{
    /**
     * Display a listing of AliExpress products.
     */
    public function index(Request $request)
    {
        $query = AliExpressProduct::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_title', 'LIKE', "%{$search}%")
                    ->orWhere('product_id', 'LIKE', "%{$search}%")
                    ->orWhere('first_level_category_name', 'LIKE', "%{$search}%")
                    ->orWhere('second_level_category_name', 'LIKE', "%{$search}%")
                    ->orWhere('shop_name', 'LIKE', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('first_level_category_name', $request->category);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('target_sale_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('target_sale_price', '<=', $request->max_price);
        }

        // Order by latest first
        $query->orderBy('created_at', 'desc');

        $products = $query->paginate(20);

        // Get categories for filter dropdown
        $categories = Cache::remember('aliexpress_categories', 3600, function () {
            return AliExpressProduct::distinct('first_level_category_name')
                ->whereNotNull('first_level_category_name')
                ->pluck('first_level_category_name')
                ->sort();
        });

        return view('admin.aliexpress.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('admin.aliexpress.products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string|unique:aliexpress_products',
            'product_title' => 'required|string|max:500',
            'target_sale_price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'product_main_image_url' => 'nullable|url',
            'product_detail_url' => 'nullable|url',
            'promotion_link' => 'required|url',
            'first_level_category_name' => 'nullable|string|max:100',
            'second_level_category_name' => 'nullable|string|max:100',
            'shop_name' => 'nullable|string|max:200',
            'shop_url' => 'nullable|url',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'latest_volume' => 'nullable|integer|min:0',
        ]);

        AliExpressProduct::create($request->all());

        return redirect()->route('admin.aliexpress.products.index')
            ->with('success', 'AliExpress product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = AliExpressProduct::findOrFail($id);

        // Get product statistics
        $viewsCount = AliExpressViewedProduct::where('aliexpress_product_id', $product->id)->count();
        $savesCount = AliExpressSavedProduct::where('aliexpress_product_id', $product->id)->count();

        return view('admin.aliexpress.products.show', compact('product', 'viewsCount', 'savesCount'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = AliExpressProduct::findOrFail($id);
        return view('admin.aliexpress.products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = AliExpressProduct::findOrFail($id);

        $request->validate([
            'product_id' => 'required|string|unique:aliexpress_products,product_id,' . $product->id,
            'product_title' => 'required|string|max:500',
            'target_sale_price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'product_main_image_url' => 'nullable|url',
            'product_detail_url' => 'nullable|url',
            'promotion_link' => 'required|url',
            'first_level_category_name' => 'nullable|string|max:100',
            'second_level_category_name' => 'nullable|string|max:100',
            'shop_name' => 'nullable|string|max:200',
            'shop_url' => 'nullable|url',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'latest_volume' => 'nullable|integer|min:0',
        ]);

        $product->update($request->all());

        return redirect()->route('admin.aliexpress.products.index')
            ->with('success', 'AliExpress product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = AliExpressProduct::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.aliexpress.products.index')
            ->with('success', 'AliExpress product deleted successfully.');
    }

    /**
     * Export products to CSV.
     */
    public function export(Request $request)
    {
        $query = AliExpressProduct::query();

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_title', 'LIKE', "%{$search}%")
                    ->orWhere('product_id', 'LIKE', "%{$search}%")
                    ->orWhere('first_level_category_name', 'LIKE', "%{$search}%")
                    ->orWhere('second_level_category_name', 'LIKE', "%{$search}%")
                    ->orWhere('shop_name', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('first_level_category_name', $request->category);
        }

        if ($request->filled('min_price')) {
            $query->where('target_sale_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('target_sale_price', '<=', $request->max_price);
        }

        $products = $query->orderBy('created_at', 'desc')->get();

        $filename = 'aliexpress_products_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'Product ID',
                'Product Title',
                'App Sale Price',
                'Original Price',
                'Sale Price',
                'Target Sale Price',
                'Target Original Price',
                'Target App Sale Price',
                'First Level Category',
                'Second Level Category',
                'Shop Name',
                'Shop ID',
                'Commission Rate',
                'Hot Product Commission Rate',
                'Latest Volume',
                'Recommendation Count',
                'Product Main Image URL',
                'Product Detail URL',
                'Promotion Link',
                'SKU ID',
                'Created At',
                'Updated At'
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->product_id,
                    $product->product_title,
                    $product->app_sale_price,
                    $product->original_price,
                    $product->sale_price,
                    $product->target_sale_price,
                    $product->target_original_price,
                    $product->target_app_sale_price,
                    $product->first_level_category_name,
                    $product->second_level_category_name,
                    $product->shop_name,
                    $product->shop_id,
                    $product->commission_rate,
                    $product->hot_product_commission_rate,
                    $product->latest_volume,
                    $product->recommendation_count,
                    $product->product_main_image_url,
                    $product->product_detail_url,
                    $product->promotion_link,
                    $product->sku_id,
                    $product->created_at,
                    $product->updated_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show trashed products.
     */
    public function trashed()
    {
        $products = AliExpressProduct::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('admin.aliexpress.products.trashed', compact('products'));
    }

    /**
     * Restore a trashed product.
     */
    public function restore($id)
    {
        $product = AliExpressProduct::onlyTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('admin.aliexpress.products.trashed')
            ->with('success', 'AliExpress product restored successfully.');
    }

    /**
     * Permanently delete a product.
     */
    public function forceDelete($id)
    {
        $product = AliExpressProduct::onlyTrashed()->findOrFail($id);
        $product->forceDelete();

        return redirect()->route('admin.aliexpress.products.trashed')
            ->with('success', 'AliExpress product permanently deleted.');
    }
}
