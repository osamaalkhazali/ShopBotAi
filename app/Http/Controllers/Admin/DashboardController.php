<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\SavedProduct;
use App\Models\AliExpressProduct;
use App\Models\AliExpressSavedProduct;
use App\Models\AliExpressViewedProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard focused on AliExpress data.
     */
    public function index()
    {
        // AliExpress stats with cache - counts don't change every second
        $totalAliExpressProducts = Cache::remember('total_aliexpress_products', 3600, function () {
            return AliExpressProduct::count();
        });

        $totalAliExpressCategories = Cache::remember('total_aliexpress_categories', 3600, function () {
            return AliExpressProduct::distinct('first_level_category_name')->whereNotNull('first_level_category_name')->count();
        });

        $totalUsers = Cache::remember('total_users', 3600, function () {
            return User::count();
        });

        $totalAliExpressViews = Cache::remember('total_aliexpress_views', 3600, function () {
            return AliExpressViewedProduct::count();
        });

        $totalAliExpressSaves = Cache::remember('total_aliexpress_saves', 3600, function () {
            return AliExpressSavedProduct::count();
        });

        // Amazon stats for comparison
        $totalAmazonProducts = Cache::remember('total_products', 3600, function () {
            return Product::count();
        });

        $totalAmazonCategories = Cache::remember('total_categories', 3600, function () {
            return Category::count();
        });

        $totalAmazonViews = Cache::remember('total_views', 3600, function () {
            return ProductView::count();
        });

        // Today's date for filtering
        $today = now()->startOfDay();

        // Count today's AliExpress views and saves
        $aliExpressViewedToday = AliExpressViewedProduct::where('created_at', '>=', $today)->count();
        $aliExpressSavedToday = AliExpressSavedProduct::where('created_at', '>=', $today)->count();

        // Count today's Amazon views and saves
        $amazonViewedToday = ProductView::where('created_at', '>=', $today)->count();
        $amazonSavedToday = SavedProduct::where('created_at', '>=', $today)->count();

        // AliExpress products viewed today with view count
        $recentlyViewedAliExpressProducts = Cache::remember('recently_viewed_aliexpress_products', 600, function () use ($today) {
            return DB::select("
                SELECT aep.*, COUNT(aevp.id) as views_count
                FROM aliexpress_products aep
                JOIN aliexpress_viewed_products aevp
                    ON aep.id = aevp.aliexpress_product_id
                WHERE aevp.created_at >= ?
                GROUP BY aep.id
                ORDER BY views_count DESC
                LIMIT 10
            ", [$today]);
        });

        // AliExpress products saved today with save count
        $recentlySavedAliExpressProducts = Cache::remember('recently_saved_aliexpress_products', 600, function () use ($today) {
            return DB::select("
                SELECT aep.*, COUNT(aesp.id) as saves_count
                FROM aliexpress_products aep
                JOIN aliexpress_saved_products aesp
                    ON aep.id = aesp.aliexpress_product_id
                WHERE aesp.created_at >= ?
                GROUP BY aep.id
                ORDER BY saves_count DESC
                LIMIT 10
            ", [$today]);
        });

        // AliExpress activity chart - last 7 days data
        $activityDates = [];
        $aliExpressViewsData = [];
        $aliExpressSavesData = [];
        $amazonViewsData = [];
        $amazonSavesData = [];

        // Get data for the past 7 days
        for ($i = 6; $i >= 0; $i--) {
            $activityDates[] = now()->subDays($i)->format('M d');

            // Count views and saves for this day
            $dayStart = now()->subDays($i)->startOfDay();
            $dayEnd = now()->subDays($i)->endOfDay();

            // AliExpress data
            $aliExpressViewsCount = AliExpressViewedProduct::whereBetween('created_at', [$dayStart, $dayEnd])->count();
            $aliExpressSavesCount = AliExpressSavedProduct::whereBetween('created_at', [$dayStart, $dayEnd])->count();

            // Amazon data
            $amazonViewsCount = ProductView::whereBetween('created_at', [$dayStart, $dayEnd])->count();
            $amazonSavesCount = SavedProduct::whereBetween('created_at', [$dayStart, $dayEnd])->count();

            $aliExpressViewsData[] = $aliExpressViewsCount;
            $aliExpressSavesData[] = $aliExpressSavesCount;
            $amazonViewsData[] = $amazonViewsCount;
            $amazonSavesData[] = $amazonSavesCount;
        }

        // AliExpress category distribution chart
        $aliExpressCategories = Cache::remember('aliexpress_category_distribution', 600, function () {
            return AliExpressProduct::select('first_level_category_name', DB::raw('count(*) as products_count'))
                ->whereNotNull('first_level_category_name')
                ->groupBy('first_level_category_name')
                ->orderBy('products_count', 'desc')
                ->limit(8)
                ->get();
        });

        $aliExpressCategoryLabels = $aliExpressCategories->pluck('first_level_category_name')->toArray();
        $aliExpressCategoryData = $aliExpressCategories->pluck('products_count')->toArray();

        // Top AliExpress Products for chart (using recently viewed as example)
        $aliexpressTopProductLabels = array_map(function ($product) {
            return $product->title ?? 'Unknown';
        }, $recentlyViewedAliExpressProducts);
        $aliexpressTopProductData = array_map(function ($product) {
            return $product->views_count ?? 0;
        }, $recentlyViewedAliExpressProducts);

        // Build stats arrays for the dashboard view
        $aliexpress_stats = [
            'total_products' => $totalAliExpressProducts,
            'total_categories' => $totalAliExpressCategories,
            'total_views' => $totalAliExpressViews,
            'total_saves' => $totalAliExpressSaves,
            'viewed_today' => $aliExpressViewedToday,
            'saved_today' => $aliExpressSavedToday,
        ];
        $amazon_stats = [
            'total_products' => $totalAmazonProducts,
            'total_categories' => $totalAmazonCategories,
            'total_views' => $totalAmazonViews,
            'total_saves' => $amazonSavedToday, // or total saves if you have it
        ];

        // Rename for blade compatibility
        $aliexpressRecentlyViewed = $recentlyViewedAliExpressProducts;
        $aliexpressRecentlySaved = $recentlySavedAliExpressProducts;
        $aliexpressActivityDates = $activityDates;
        $aliexpressViewsData = $aliExpressViewsData;
        $aliexpressSavesData = $aliExpressSavesData;
        $aliexpressCategoryLabels = $aliExpressCategoryLabels;
        $aliexpressCategoryData = $aliExpressCategoryData;

        // Map image and title fields for dashboard compatibility
        $aliexpressRecentlyViewed = array_map(function ($product) {
            $product->imgUrl = $product->product_main_image_url ?? null;
            $product->title = $product->product_title ?? ($product->title ?? null);
            return $product;
        }, $recentlyViewedAliExpressProducts);
        $aliexpressRecentlySaved = array_map(function ($product) {
            $product->imgUrl = $product->product_main_image_url ?? null;
            $product->title = $product->product_title ?? ($product->title ?? null);
            return $product;
        }, $recentlySavedAliExpressProducts);

        return view('admin.dashboard', compact(
            'aliexpress_stats',
            'amazon_stats',
            'totalUsers',
            'aliexpressRecentlyViewed',
            'aliexpressRecentlySaved',
            'aliexpressActivityDates',
            'aliexpressViewsData',
            'aliexpressSavesData',
            'amazonViewsData',
            'amazonSavesData',
            'aliexpressCategoryLabels',
            'aliexpressCategoryData',
            'aliexpressTopProductLabels',
            'aliexpressTopProductData'
        ));
    }

    public function adminsList(Request $request)
    {
        // Apply filters if provided
        $adminsQuery = Admin::query();
        $trashedQuery = Admin::onlyTrashed();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $adminsQuery->whereFullText('name', $search)
                ->orWhereFullText('email', $search);

            $trashedQuery->whereFullText('name', $search)
                ->orWhereFullText('email', $search);
        }

        // Role filtering
        if ($request->filled('role')) {
            $adminsQuery->where('role', $request->input('role'));
            $trashedQuery->where('role', $request->input('role'));
        }

        // Apply sorting
        $sortField = 'name';
        $sortDirection = 'asc';

        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            if ($sort === 'name_asc') {
                $sortField = 'name';
                $sortDirection = 'asc';
            } elseif ($sort === 'name_desc') {
                $sortField = 'name';
                $sortDirection = 'desc';
            } elseif ($sort === 'created_asc') {
                $sortField = 'created_at';
                $sortDirection = 'asc';
            } elseif ($sort === 'created_desc') {
                $sortField = 'created_at';
                $sortDirection = 'desc';
            }
        }

        $adminsQuery->orderBy($sortField, $sortDirection);

        // Paginate the results - 10 admins per page
        $admins = $adminsQuery->paginate(10)->withQueryString();
        $trashedAdmins = $trashedQuery->get(); // No need to paginate deleted admins as they're typically few

        return view('admin.admins.index', compact('admins', 'trashedAdmins'));
    }

    public function editAdmin($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.admins.edit', compact('admin'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $admin->id,
            'role' => 'nullable|string|in:admin,editor,super_admin', // Validate role
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $admin->password = Hash::make($request->password);
        }

        // Only super_admin can change roles
        if (Auth::guard('admin')->user()->role === 'super_admin' && $request->has('role')) {
            $admin->role = $request->role;
        }

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->save();

        return redirect()->route('admin.admins')->with('success', 'Admin updated successfully');
    }

    public function deleteAdmin($id)
    {
        $admin = Admin::findOrFail($id);

        // Prevent deletion of own account or another super_admin by non super_admin
        if (
            $admin->id === Auth::guard('admin')->id() ||
            ($admin->role === 'super_admin' && Auth::guard('admin')->user()->role !== 'super_admin')
        ) {
            return redirect()->route('admin.admins')->with('error', 'Cannot delete this admin');
        }

        $admin->delete();
        return redirect()->route('admin.admins')->with('success', 'Admin deleted successfully');
    }

    public function restoreAdmin($id)
    {
        $admin = Admin::onlyTrashed()->findOrFail($id);
        $admin->restore();
        return redirect()->route('admin.admins')->with('success', 'Admin restored successfully');
    }

    public function forceDeleteAdmin($id)
    {
        $admin = Admin::onlyTrashed()->findOrFail($id);
        $admin->forceDelete();
        return redirect()->route('admin.admins')->with('success', 'Admin permanently deleted');
    }

    /**
     * Export admins data to CSV or PDF
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportAdmins(Request $request)
    {
        // Get export format and scope
        $format = $request->input('format', 'csv');
        $exportScope = $request->input('export_scope', 'current_page');
        $perPage = $exportScope === 'current_page' ? 10 : 1000; // Large number for "all" pages
        $page = $request->input('page', 1);

        // Create base query
        $query = Admin::query();

        // Apply trashed filter if present
        if ($request->has('trashed')) {
            $query->onlyTrashed();
        }

        // Apply filters if provided
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filtering
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Apply sorting
        $sortField = 'name';
        $sortDirection = 'asc';

        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            if ($sort === 'name_asc') {
                $sortField = 'name';
                $sortDirection = 'asc';
            } elseif ($sort === 'name_desc') {
                $sortField = 'name';
                $sortDirection = 'desc';
            } elseif ($sort === 'created_asc') {
                $sortField = 'created_at';
                $sortDirection = 'asc';
            } elseif ($sort === 'created_desc') {
                $sortField = 'created_at';
                $sortDirection = 'desc';
            } elseif ($sort === 'email_asc') {
                $sortField = 'email';
                $sortDirection = 'asc';
            } elseif ($sort === 'email_desc') {
                $sortField = 'email';
                $sortDirection = 'desc';
            } elseif ($sort === 'role_asc') {
                $sortField = 'role';
                $sortDirection = 'asc';
            } elseif ($sort === 'role_desc') {
                $sortField = 'role';
                $sortDirection = 'desc';
            }
        }

        $query->orderBy($sortField, $sortDirection);

        // Get admins based on export scope
        if ($exportScope === 'current_page') {
            $admins = $query->paginate($perPage, ['*'], 'page', $page);
        } else {
            $admins = $query->get();
        }

        // Filename
        $filename = 'admins_export_' . date('Y_m_d_H_i_s');

        // Headers for the export
        $headers = [
            '#',
            'Name',
            'Email',
            'Role',
            'Created At',
            'Updated At'
        ];

        // Prepare data for export
        $data = [];
        $counter = 1;

        foreach ($admins as $admin) {
            $data[] = [
                $counter++,
                $admin->name,
                $admin->email,
                ucfirst($admin->role),
                $admin->created_at ? $admin->created_at->format('Y-m-d H:i:s') : 'N/A',
                $admin->updated_at ? $admin->updated_at->format('Y-m-d H:i:s') : 'N/A'
            ];
        }

        if ($format === 'csv') {
            return response()->stream(function () use ($filename, $headers, $data) {
                $this->exportToCsv($filename, $headers, $data);
            });
        } else {
            return $this->exportToPdf($filename, $headers, $data, 'Admins List');
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
