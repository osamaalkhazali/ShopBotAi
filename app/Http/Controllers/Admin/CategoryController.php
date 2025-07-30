<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Apply filters
        if ($request->filled('category_name')) {
            $query->where('category_name', 'like', '%' . $request->category_name . '%');
        }

        // Get product count for each category
        $query->withCount('products');

        if ($request->filled('sort')) {
            $sortParts = explode('_', $request->sort);
            $sortField = $sortParts[0] ?? 'created_at';
            $sortDirection = $sortParts[1] ?? 'asc';

            // Validate sort direction
            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'asc';
            }

            // Map frontend sort fields to actual database columns
            if ($sortField === 'category') {
                $sortField = 'category_name';
            } elseif ($sortField === 'products') {
                $sortField = 'products_count';
            }

            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $categories = $query->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:255|unique:categories',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Category::create($request->all());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        $products = Product::where('category_id', $id)->paginate(15);

        return view('admin.categories.show', compact('category', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:255|unique:categories,category_name,' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $category->update($request->all());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        // Check if the category has products
        $productsCount = Product::where('category_id', $id)->count();

        if ($productsCount > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category because it has ' . $productsCount . ' associated products. Reassign or delete the products first.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    /**
     * Restore a soft deleted category
     */
    public function restore($id)
    {
        Category::withTrashed()->findOrFail($id)->restore();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category restored successfully!');
    }

    /**
     * Force delete a category permanently
     */
    public function forceDelete($id)
    {
        Category::withTrashed()->findOrFail($id)->forceDelete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category permanently deleted!');
    }

    /**
     * Show trashed categories
     */
    public function trashed()
    {
        $categories = Category::onlyTrashed()->withCount('products')->paginate(15);
        return view('admin.categories.trashed', compact('categories'));
    }

    /**
     * Export categories data to CSV or PDF
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
        $query = Category::query();

        // Apply trashed filter if present
        if ($request->filled('status') && $request->input('status') === 'trashed') {
            $query->onlyTrashed();
        }

        // Apply filters if provided
        if ($request->filled('category_name')) {
            $query->where('category_name', 'like', '%' . $request->category_name . '%');
        }

        // Get product count for each category
        $query->withCount('products');

        // Apply sorting
        if ($request->filled('sort')) {
            $sortParts = explode('_', $request->sort);
            $sortField = $sortParts[0] ?? 'created_at';
            $sortDirection = $sortParts[1] ?? 'asc';

            // Validate sort direction
            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'asc';
            }

            // Map frontend sort fields to actual database columns
            if ($sortField === 'category') {
                $sortField = 'category_name';
            } elseif ($sortField === 'products') {
                $sortField = 'products_count';
            }

            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Get categories based on export scope
        if ($exportScope === 'current_page') {
            $categories = $query->paginate($perPage, ['*'], 'page', $page);
        } else {
            $categories = $query->get();
        }

        // Filename
        $filename = 'categories_export_' . date('Y_m_d_H_i_s');

        // Headers for the export
        $headers = [
            '#',
            'Category Name',
            'Products Count',
            'Created At',
            'Updated At',
            'Status'
        ];

        // Prepare data for export
        $data = [];
        $counter = 1;

        foreach ($categories as $category) {
            $data[] = [
                $counter++,
                $category->category_name,
                $category->products_count,
                $category->created_at ? $category->created_at->format('Y-m-d H:i:s') : 'N/A',
                $category->updated_at ? $category->updated_at->format('Y-m-d H:i:s') : 'N/A',
                $category->deleted_at ? 'Deleted' : 'Active'
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
            return $this->exportToPdf($filename, $headers, $data, 'Categories List');
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
        return new \Illuminate\Http\Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
