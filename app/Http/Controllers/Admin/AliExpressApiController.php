<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Include helper functions
require_once app_path('Helpers/helpers.php');

class AliExpressApiController extends Controller
{
    /**
     * Display the API test interface.
     */
    public function index()
    {
        return view('admin.aliexpress.api.index');
    }

    /**
     * Test AliExpress API connection.
     */
    public function testConnection(Request $request)
    {
        try {
            $response = Http::timeout(30)->get('https://serpapi.com/search.json', [
                'engine' => 'aliexpress',
                'search_query' => 'test',
                'api_key' => env('SERPAPI_KEY'),
                'gl' => 'us',
                'hl' => 'en'
            ]);

            $statusCode = $response->status();
            $responseData = $response->json();

            $result = [
                'success' => $statusCode === 200,
                'status_code' => $statusCode,
                'response_time' => $response->handlerStats()['total_time'] ?? 'N/A',
                'data' => $responseData,
                'message' => $statusCode === 200 ? 'API connection successful' : 'API connection failed'
            ];

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AliExpress API test failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status_code' => 500,
                'response_time' => 'N/A',
                'error' => $e->getMessage(),
                'message' => 'API connection failed'
            ]);
        }
    }

    /**
     * Test product search functionality.
     */
    public function testSearch(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:255',
        ]);

        try {
            $response = Http::timeout(30)->get('https://serpapi.com/search.json', [
                'engine' => 'aliexpress',
                'search_query' => $request->query,
                'api_key' => env('SERPAPI_KEY'),
                'gl' => 'us',
                'hl' => 'en',
                'page' => 1
            ]);

            $statusCode = $response->status();
            $responseData = $response->json();

            $result = [
                'success' => $statusCode === 200,
                'status_code' => $statusCode,
                'query' => $request->query,
                'results_count' => isset($responseData['organic_results']) ? count($responseData['organic_results']) : 0,
                'data' => $responseData,
                'message' => $statusCode === 200 ? 'Search successful' : 'Search failed'
            ];

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AliExpress search test failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status_code' => 500,
                'query' => $request->query,
                'error' => $e->getMessage(),
                'message' => 'Search test failed'
            ]);
        }
    }

    /**
     * Test product details retrieval.
     */
    public function testProductDetails(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string',
        ]);

        try {
            // For AliExpress, we might need to construct a product URL or use a different approach
            // This is a placeholder implementation
            $response = Http::timeout(30)->get('https://serpapi.com/search.json', [
                'engine' => 'aliexpress',
                'search_query' => $request->product_id,
                'api_key' => env('SERPAPI_KEY'),
                'gl' => 'us',
                'hl' => 'en'
            ]);

            $statusCode = $response->status();
            $responseData = $response->json();

            $result = [
                'success' => $statusCode === 200,
                'status_code' => $statusCode,
                'product_id' => $request->product_id,
                'data' => $responseData,
                'message' => $statusCode === 200 ? 'Product details retrieved successfully' : 'Failed to retrieve product details'
            ];

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AliExpress product details test failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status_code' => 500,
                'product_id' => $request->product_id,
                'error' => $e->getMessage(),
                'message' => 'Product details test failed'
            ]);
        }
    }

    /**
     * Get API configuration and status.
     */
    public function getApiStatus()
    {
        $apiKey = env('SERPAPI_KEY');

        $status = [
            'api_key_configured' => !empty($apiKey),
            'api_key_length' => $apiKey ? strlen($apiKey) : 0,
            'api_key_masked' => $apiKey ? substr($apiKey, 0, 8) . '...' . substr($apiKey, -4) : 'Not configured',
            'base_url' => 'https://serpapi.com/search.json',
            'engine' => 'aliexpress',
            'timeout' => 30,
            'default_params' => [
                'gl' => 'us',
                'hl' => 'en'
            ]
        ];

        return response()->json($status);
    }

    /**
     * Test batch product retrieval.
     */
    public function testBatchProducts(Request $request)
    {
        $request->validate([
            'queries' => 'required|array|min:1|max:5',
            'queries.*' => 'required|string|max:255',
        ]);

        $results = [];
        $totalTime = 0;

        foreach ($request->queries as $index => $query) {
            try {
                $startTime = microtime(true);

                $response = Http::timeout(30)->get('https://serpapi.com/search.json', [
                    'engine' => 'aliexpress',
                    'search_query' => $query,
                    'api_key' => env('SERPAPI_KEY'),
                    'gl' => 'us',
                    'hl' => 'en',
                    'page' => 1
                ]);

                $endTime = microtime(true);
                $requestTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
                $totalTime += $requestTime;

                $statusCode = $response->status();
                $responseData = $response->json();

                $results[] = [
                    'query' => $query,
                    'success' => $statusCode === 200,
                    'status_code' => $statusCode,
                    'response_time' => round($requestTime, 2) . 'ms',
                    'results_count' => isset($responseData['organic_results']) ? count($responseData['organic_results']) : 0,
                    'data' => $responseData
                ];

                // Add a small delay between requests to avoid rate limiting
                if ($index < count($request->queries) - 1) {
                    usleep(500000); // 0.5 second delay
                }
            } catch (\Exception $e) {
                $results[] = [
                    'query' => $query,
                    'success' => false,
                    'status_code' => 500,
                    'response_time' => 'N/A',
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'total_queries' => count($request->queries),
            'successful_queries' => count(array_filter($results, fn($r) => $r['success'])),
            'total_time' => round($totalTime, 2) . 'ms',
            'average_time' => round($totalTime / count($request->queries), 2) . 'ms',
            'results' => $results
        ]);
    }

    /**
     * Test the AliExpressProducts helper function.
     */
    public function testHelperProducts(Request $request)
    {
        $request->validate([
            'keywords' => 'nullable|string|max:255',
            'category_ids' => 'nullable|string|max:255',
            'page_size' => 'nullable|integer|min:1|max:50',
        ]);

        try {
            $startTime = microtime(true);

            // Prepare parameters for the helper function
            $params = [
                'keywords' => $request->input('keywords', 'wireless headphones'),
                'category_ids' => $request->input('category_ids', ''),
                'page_size' => $request->input('page_size', 10),
                'page_no' => 1,
            ];

            // Call the helper function
            $response = AliExpressProducts($params);

            $endTime = microtime(true);
            $requestTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

            // Check if response has error
            if (isset($response['error']) && $response['error'] === true) {
                return response()->json([
                    'success' => false,
                    'message' => $response['message'] ?? 'Helper function returned error',
                    'code' => $response['code'] ?? 500,
                    'response_time' => round($requestTime, 2) . 'ms',
                    'params' => $params
                ]);
            }

            // Check if we have a valid response structure
            $products = $response['aliexpress_affiliate_product_query_response']['resp_result']['result']['products']['product'] ?? [];
            $totalResults = $response['aliexpress_affiliate_product_query_response']['resp_result']['result']['total_record_count'] ?? 0;

            $result = [
                'success' => true,
                'message' => 'AliExpressProducts helper function test successful',
                'response_time' => round($requestTime, 2) . 'ms',
                'params' => $params,
                'total_results' => $totalResults,
                'products_returned' => count($products),
                'sample_products' => array_slice($products, 0, 3), // Show first 3 products as sample
                'raw_response' => $response
            ];

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AliExpressProducts helper test failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Helper function test failed',
                'error' => $e->getMessage(),
                'params' => $params ?? []
            ]);
        }
    }

    /**
     * Test the AliExpressCategories helper function.
     */
    public function testHelperCategories(Request $request)
    {
        try {
            $startTime = microtime(true);

            // Call the helper function
            $response = AliExpressCategories();

            $endTime = microtime(true);
            $requestTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

            // Check if response has error
            if (isset($response['error']) && $response['error'] === true) {
                return response()->json([
                    'success' => false,
                    'message' => $response['message'] ?? 'Helper function returned error',
                    'code' => $response['code'] ?? 500,
                    'response_time' => round($requestTime, 2) . 'ms'
                ]);
            }

            // Check if we have a valid response structure
            $categories = $response['aliexpress_affiliate_category_get_response']['resp_result']['result']['categories']['category'] ?? [];

            $result = [
                'success' => true,
                'message' => 'AliExpressCategories helper function test successful',
                'response_time' => round($requestTime, 2) . 'ms',
                'categories_count' => count($categories),
                'sample_categories' => array_slice($categories, 0, 5), // Show first 5 categories as sample
                'raw_response' => $response
            ];

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AliExpressCategories helper test failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Helper function test failed',
                'error' => $e->getMessage()
            ]);
        }
    }
}
