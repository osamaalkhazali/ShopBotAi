<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Start with a base query - use DB query builder for better performance with large datasets
        // Only select the necessary columns to reduce memory usage
        $query = Product::query()
            ->select(
                'products.id',
                'products.asin',
                'products.title',
                'products.imgUrl',
                'products.productURL',
                'products.stars',
                'products.reviews',
                'products.price',
                'products.listPrice',
                'products.category_id',
                'products.isBestSeller'
            )
            ->with(['category' => function ($query) {
                $query->select('id', 'category_name');
            }]);

        // Apply filters
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        // Apply sorting
        if ($request->filled('sort')) {
            $sort = explode('_', $request->input('sort'));
            $column = $sort[0] ?? 'created_at';
            $direction = $sort[1] ?? 'desc';
            $query->orderBy($column, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Use efficient database aggregations instead of loading all records
        $today = now()->startOfDay();

        // Efficient count for totals using raw SQL queries
        $viewedToday = cache()->remember('viewed_today', 60, function () use ($today) {
            return DB::table('product_views')
            ->where('created_at', '>=', $today)
            ->count();
        });

        $savedToday = cache()->remember('saved_today', 60, function () use ($today) {
            return DB::table('saved_products')
            ->where('created_at', '>=', $today)
            ->count();
        });

        // Paginate with a small number of items 
        $products = $query->paginate(10);

        // Get categories efficiently
        $categories = Category::select('id', 'category_name')
            ->orderBy('category_name')
            ->get();

        // Efficient way to get today's stats only for the current page of products
        $productIds = $products->pluck('id')->toArray();

        if (!empty($productIds)) {
            // Get today's views for only the current page products
            $todayViewsQuery = DB::table('product_views')
                ->select('product_id', DB::raw('COUNT(*) as today_views'))
                ->where('created_at', '>=', $today)
                ->whereIn('product_id', $productIds)
                ->groupBy('product_id');

            $todayViews = $todayViewsQuery->pluck('today_views', 'product_id')->toArray();

            // Get today's saves for only the current page products
            $todaySavesQuery = DB::table('saved_products')
                ->select('product_id', DB::raw('COUNT(*) as today_saves'))
                ->where('created_at', '>=', $today)
                ->whereIn('product_id', $productIds)
                ->groupBy('product_id');

            $todaySaves = $todaySavesQuery->pluck('today_saves', 'product_id')->toArray();

            // Add stats to products
            foreach ($products as $product) {
                $product->todayViews = $todayViews[$product->id] ?? 0;
                $product->todaySaves = $todaySaves[$product->id] ?? 0;
            }
        }

        return view('admin.products.index', compact('products', 'categories', 'viewedToday', 'savedToday'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asin' => 'required|string|max:255|unique:products',
            'title' => 'required|string|max:255',
            'imgUrl' => 'required|string',
            'productURL' => 'required|string',
            'stars' => 'required|numeric|min:0|max:5',
            'reviews' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'listPrice' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'isBestSeller' => 'boolean',
            'boughtInLastMonth' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Product::create($request->all());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('category')->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'asin' => 'required|string|max:255|unique:products,asin,' . $id,
            'title' => 'required|string|max:255',
            'imgUrl' => 'required|string',
            'productURL' => 'required|string',
            'stars' => 'required|numeric|min:0|max:5',
            'reviews' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'listPrice' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'isBestSeller' => 'boolean',
            'boughtInLastMonth' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $product->update($request->all());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Restore a soft deleted product
     */
    public function restore($id)
    {
        Product::withTrashed()->findOrFail($id)->restore();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product restored successfully!');
    }

    /**
     * Force delete a product permanently
     */
    public function forceDelete($id)
    {
        Product::withTrashed()->findOrFail($id)->forceDelete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product permanently deleted!');
    }

    /**
     * Show trashed products
     */
    public function trashed()
    {
        $products = Product::onlyTrashed()->with('category')->paginate(15);
        return view('admin.products.trashed', compact('products'));
    }

    /**
     * Toggle the best seller status of a product
     */
    public function toggleBestSeller($id)
    {
        $product = Product::findOrFail($id);
        $product->isBestSeller = !$product->isBestSeller;
        $product->save();

        return response()->json([
            'success' => true,
            'isBestSeller' => $product->isBestSeller,
            'message' => $product->isBestSeller ? 'Product marked as best seller' : 'Product removed from best sellers'
        ]);
    }

    /**
     * Export products data to CSV or PDF
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        // Get export format and scope
        $format = $request->input('format', 'csv');
        $exportScope = $request->input('export_scope', 'current_page');
        $perPage = $exportScope === 'current_page' ? 15 : 1000;
        $page = $request->input('page', 1);

        // Create base query
        $query = Product::query();

        // Apply trashed filter if present
        if ($request->filled('status') && $request->input('status') === 'trashed') {
            $query->onlyTrashed();
        }

        // Apply search filters - accept both 'search' and 'title' parameters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('asin', 'like', "%{$search}%");
            });
        } elseif ($request->filled('title')) {
            $search = $request->input('title');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('asin', 'like', "%{$search}%");
            });
        }

        // Category filter - accept both 'category' and 'category_id' parameters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        } elseif ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Best seller filter
        if ($request->filled('best_seller')) {
            $query->where('isBestSeller', $request->best_seller === 'yes');
        }

        // Price range filter - accept both min_price/max_price and price_min/price_max
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        } elseif ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        } elseif ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Apply sorting
        if ($request->filled('sort')) {
            $sort = $request->input('sort');

            // Check if sort is already in the format 'field_direction'
            if (strpos($sort, '_') !== false) {
                $sortParts = explode('_', $sort);
                $sortField = $sortParts[0];
                $sortDirection = $sortParts[1] ?? 'asc';

                switch ($sortField) {
                    case 'price':
                        $query->orderBy('price', $sortDirection);
                        break;
                    case 'title':
                        $query->orderBy('title', $sortDirection);
                        break;
                    case 'stars':
                        $query->orderBy('stars', $sortDirection);
                        break;
                    default:
                        $query->orderBy($sortField, $sortDirection);
                }
            } else {
                // Default direction if not specified
                $sortDirection = $request->input('direction', 'desc');
                $query->orderBy($sort, $sortDirection);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Join with categories for better data
        $query->with('category');

        // Get products based on export scope
        if ($exportScope === 'current_page') {
            $products = $query->paginate($perPage, ['*'], 'page', $page);
        } else {
            $products = $query->get();
        }

        // Filename
        $filename = 'products_export_' . date('Y_m_d_H_i_s');

        // Headers for the export
        $headers = [
            '#',
            'ASIN',
            'Title',
            'Category',
            'Price',
            'List Price',
            'Rating',
            'Reviews',
            'Best Seller',
            'Created At'
        ];

        // Prepare data for export
        $data = [];
        $counter = 1;

        foreach ($products as $product) {
            $data[] = [
                $counter++,
                $product->asin,
                $product->title,
                $product->category ? $product->category->category_name : 'N/A',
                '$' . number_format($product->price, 2),
                $product->listPrice ? '$' . number_format($product->listPrice, 2) : 'N/A',
                $product->stars . '/5',
                number_format($product->reviews),
                $product->isBestSeller ? 'Yes' : 'No',
                $product->created_at ? $product->created_at->format('Y-m-d H:i:s') : 'N/A',
            ];
        }

        if ($format === 'csv') {
            return response()->stream(function () use ($filename, $headers, $data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $headers);

                foreach ($data as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            ]);
        } else {
            return $this->exportToPdf($filename, $headers, $data, 'Products List');
        }
    }

    /**
     * Export data to CSV file
     *
     * @param string $filename
     * @param array $headers
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function exportToCsv($filename, $headers, $data)
    {
        $filename = $filename . '.csv';

        $callback = function () use ($headers, $data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export data to PDF file
     *
     * @param string $filename
     * @param array $headers
     * @param array $data
     * @param string $title
     * @return \Illuminate\Http\Response
     */
    private function exportToPdf($filename, $headers, $data, $title)
    {
        $filename = $filename . '.pdf';

        // Generate HTML for PDF
        $html = '<style>
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #064e3b;
                color: white;
            }
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            .title {
                text-align: center;
                margin-bottom: 20px;
                font-size: 24px;
            }
            .footer {
                text-align: center;
                margin-top: 20px;
                font-size: 12px;
                color: #666;
            }
        </style>';

        $html .= '<div class="title">' . $title . '</div>';
        $html .= '<table>';

        // Add headers
        $html .= '<tr>';
        foreach ($headers as $header) {
            $html .= '<th>' . $header . '</th>';
        }
        $html .= '</tr>';

        // Add data rows
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . $cell . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '<div class="footer">Generated on ' . date('Y-m-d H:i:s') . '</div>';

        // Generate PDF using Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Stream PDF to browser
        return response()->make($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
