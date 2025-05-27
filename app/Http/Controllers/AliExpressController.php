<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\AliExpressProduct;
use OpenAI;


class AliExpressController extends Controller
{
    /**
     * Search AliExpress products based on keywords and other parameters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'keywords' => 'nullable|string',
            'category_ids' => 'nullable|string',
            'page_no' => 'nullable|integer|min:1',
            'page_size' => 'nullable|integer|min:1|max:50',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|gte:min_price',
            'sort' => 'nullable|string',
            'ship_to_country' => 'nullable|string',
            'target_currency' => 'nullable|string|size:3',
            'target_language' => 'nullable|string|size:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            // Prepare parameters for AliExpressProducts helper
            $params = [
                'keywords' => $request->input('keywords', ''),
                'category_ids' => $request->input('category_ids', ''),
                'max_sale_price' => $request->input('max_price', '1000000000'),
                'min_sale_price' => $request->input('min_price', '0'),
                'page_no' => $request->input('page_no', 1),
                'page_size' => $request->input('page_size', 20),
                'sort' => $request->input('sort', ''),
                'target_currency' => $request->input('target_currency', 'USD'),
                'target_language' => $request->input('target_language', 'EN'),
                'ship_to_country' => $request->input('ship_to_country', ''),
            ];

            // Use the helper function to get products
            $products = AliExpressProducts($params);

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 500);
        }
    }

    /**
     * Recommend products from AliExpress using AI analysis
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommendWithAI(Request $request)
    {
        try {
            $userPrompt = $request->get('query', '');
            $maxPrice = $request->input('max_price');

            // First, generate the friendly message
            $friendlyMessage = $this->generateFriendlyMessage($userPrompt);

            // Return the friendly message immediately, while products will be fetched separately
            return response()->json([
                'success' => true,
                'data' => [
                    'friendly_message' => $friendlyMessage,
                    'user_query' => $userPrompt,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('AliExpress AI friendly message error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a friendly AI message based on user query
     *
     * @param string $userPrompt
     * @return string
     */
    protected function generateFriendlyMessage($userPrompt)
    {
        // Create OpenAI client
        $client = OpenAI::factory()
            ->withApiKey(config('services.openai.key'))
            ->withHttpClient(new \GuzzleHttp\Client(['verify' => false]))
            ->make();

        Log::info('Generating friendly AI message for query:', ['query' => $userPrompt]);

        // Generate friendly response message
        $friendlyMessageResponse = $client->chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => "You are a helpful, enthusiastic shopping assistant for AliExpress. You need to create a friendly, conversational message in response to the user's query. Your response should be substantial but concise (4-7 sentences) and include:

                1. Begin with a warm, personalized greeting based on their query
                2. For all queries, include 3-5 key points/suggestions/ideas as bullet points using • or - symbol
                3. ALWAYS include 1-2 specific questions about preferences, concerns, or requirements within your message
                4. End with an encouraging statement that invites further conversation

                Guidelines based on query type:

                - If they're looking for a gift: Ask specific questions about the recipient (age, gender, interests, hobbies, budget). Include bullet points with gift categories that might work well.

                - If they're looking for clothes/fashion: Ask about specific preferences (size, color, style, occasion, season). Include bullet points with trendy suggestions.

                - If they're looking for electronics: Ask about specific features they need, budget constraints, or use cases. Include bullet points with helpful shopping tips.

                - If they're looking for home items: Ask about home style, room purpose, color schemes, or space constraints. Include bullet points with decor ideas.

                Use a natural conversational style with occasional emojis. Don't mention keywords or search terms. Format your response with proper spacing between paragraphs and bullet points. DO NOT use markdown formatting. Send your message as if you're a friendly, knowledgeable shopping assistant having a conversation with a friend. ALL follow-up questions must be embedded in this initial message."],
                ['role' => 'user', 'content' => "Generate a friendly shopping response for this query: {$userPrompt}"]
            ],
        ]);

        return $friendlyMessageResponse->choices[0]->message->content;
    }

    /**
     * Fetch recommended products based on the user query
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchRecommendedProducts(Request $request)
    {
        try {
            // Create OpenAI client
            $client = OpenAI::factory()
                ->withApiKey(config('services.openai.key'))
                ->withHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                ->make();

            $userPrompt = $request->get('query', '');
            $maxPrice = $request->input('max_price');

            Log::info('Starting AliExpress product recommendation process for query:', ['query' => $userPrompt]);

            // Get categories as array (not JSON response)
            $categoriesResponse = $this->getCategories();
            $categoriesData = $categoriesResponse->getData(true);
            $categories = $categoriesData['data'] ?? [];
            // Build a readable categories string for GPT: "ID: Name" per line
            $categoriesString = json_encode(
                array_map(
                    fn($category) => [
                        'id' => $category['id'] ?? '',
                        'name' => $category['name'] ?? ''
                    ],
                    $categories
                ),
                JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            );

            $systemMessage = <<<EOT
                You are an expert fashion and product search keyword generator for AliExpress API queries.

                Your job is to extract rich, detailed search parameters from user queries, especially for clothing, shoes, and accessories.

                Strict rules:

                1. **Language Detection**:
                - Detect the input language, but return results ALWAYS in English.

                2. **Keywords Extraction**:
                 - The **first keyword** is the main product phrase from the user's query (remove filler words like "i", "gift", "for", "need", etc.), 1–3 meaningful words, lowercase, no translation.
                - Extract 3–14 multi-word features (1–3 words each) about product type, color, style, material, fit, length, usage, audience, or functionality. No generic/filler words or price info.
                - For smart products, always include at least one well-known brand (e.g., "XIAOMI smartwatch").
                - Add 3–5 single-word core keywords (e.g., dress, red, chiffon).
                - If user asks for all black outfit, include "black" in most keywords.
                - All keywords: lowercase, comma-separated, no duplicates, at least 10 total (1 cleaned original + multi-word features + single-word terms).

                📌 **Example**:
                User Input:  `i need gift for my wife, a red dress`
                Output:
                ```json
                {
                "keywords": "red dress,red,romantic,floral dress,ankle-length dress,comfortable fit,lightweight fabric,versatile style,romantic gift,women's clothing,elegant outfit"
                }
                📌 **Example**:
                User Input:  `Find me a stylish smartwatch under $50`
                Output:
                ```json
                {
                "keywords": "XIAOMI smartwatch,smartwatch,wearable technology,fitness tracker,smartwatch stylish"
                }

                3. **Price Limit**:
                - Extract max price if user mentions it.
                - Convert currency to USD if needed.

                4. **Category Names**:
                - Analyze the extracted keywords and user intent to select the most relevant category names from the provided list.
                - Only choose categories where at least one keyword directly matches or is highly relevant to the category's name or typical products.
                - **Do NOT create new category names** — use only from the provided list.
                - Do NOT guess or assign unrelated categories. If no clear match exists, set `category_ids` and `category_names` to empty strings.
                - Use ONLY the category IDs and names from the JSON array below (each with "id" and "name").
                - Output the IDs as a single comma-separated string (e.g., 123,456), with NO spaces, NO quotes, and NO extra text.
                - Output the names as a single comma-separated string (e.g., kitchen gadgets,chef tools), with NO quotes and NO extra text.
                ### Categories:
                {$categoriesString}
                - Example: `i need gift for my chef friend`
                output should be related to kitchen, chef tools,gifts etc.
                - give me at least 6 categories and DON'T ever create new one
                - If user need smart thing there is a category called Smart Electronics, use it.

                ⚠️ RETURN VALID JSON ONLY — NO COMMENTS, NO EXTRAS

                Response format:
                {
                "keywords": "exact user query,feature 1,feature 2,...",
                "category_names": "category name 1,category name 2,...etc",
                "max_price": 99990, default is ''
                "sort": "orders_desc",
                "detected_language": "english"
                }

                **NEVER return less than 5 keywords AND NEVER return keywords "gift". Be specific and helpful for product search.
                EOT;

            // Use OpenAI to analyze the user's prompt and extract relevant keywords and parameters
            $extractResponse = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemMessage
                    ],
                    [
                        'role' => 'user',
                        'content' => "Extract search parameters for AliExpress API from: {$userPrompt}"
                    ]
                ],
                'response_format' => ['type' => 'json_object'],
            ]);


            $parsed = json_decode($extractResponse->choices[0]->message->content, true);

            Log::info('AI raw extraction response:', ['content' => $extractResponse->choices[0]->message->content]);

            // Extract parameters from AI response
            $extractedKeywords = $parsed['keywords'] ?? '';
            $categoryNames = $parsed['category_names'] ?? '';
            $extractedPrice = $parsed['max_price'] ?? null;
            $sort = $parsed['sort'] ?? '';
            $detectedLanguage = $parsed['detected_language'] ?? 'english';

            // Get category IDs directly from response or map from names
            $categoryIds = $parsed['category_ids'] ?? '';

            // If no category IDs but we have category names, map the names to IDs as fallback
            if (empty($categoryIds) && !empty($categoryNames)) {
                $categoryNameArray = array_map('trim', explode(',', $categoryNames));
                $mappedCategoryIds = [];

                foreach ($categories as $category) {
                    if (in_array($category['name'], $categoryNameArray)) {
                        $mappedCategoryIds[] = $category['id'];
                    }
                }

                $categoryIds = implode(',', $mappedCategoryIds);
                Log::info('Mapped category names to IDs:', ['names' => $categoryNames, 'ids' => $categoryIds]);
            }

            // Use extracted price if no explicit max_price was provided
            if (!$maxPrice && $extractedPrice) {
                $maxPrice = $extractedPrice;
            }

            Log::info('AI extracted parameters:', [
                'keywords' => $extractedKeywords,
                'category_names' => $categoryNames,
                'category_ids' => $categoryIds,
                'max_price' => $maxPrice,
                'sort' => $sort,
                'detected_language' => $detectedLanguage
            ]);

            // Convert comma-separated keywords string to array and trim each keyword
            $keywordsString = $extractedKeywords;
            $extractedKeywords = array_map('trim', explode(',', $extractedKeywords));

            $products = [];
            $allProducts = [];

            // Loop through all extracted keywords and fetch products for each
            foreach ($extractedKeywords as $keyword) {
                $params = [
                    'keywords' => $keyword,
                    'category_ids' => $categoryIds,
                    'max_sale_price' => $maxPrice ?? '1000000000',
                    'min_sale_price' => '0',
                    'page_no' => 1,
                    'page_size' => 50,
                    'sort' => $sort,
                    'target_currency' => '',
                    'target_language' => '',
                    'ship_to_country' => '',
                ];

                $productsAPI = AliExpressProducts($params);
                $fetchedProducts = $productsAPI['aliexpress_affiliate_product_query_response']['resp_result']['result']['products']['product'] ?? [];
                $allProducts = array_merge($allProducts, $fetchedProducts);
            }

            // Optionally, remove duplicate products by product ID
            $products = [];
            $productIds = [];
            foreach ($allProducts as $product) {
                if (!in_array($product['product_id'], $productIds)) {
                    $products[] = $product;
                    $productIds[] = $product['product_id'];
                }
            }

            $result = array_map(function ($product) {
                return [
                    'product_id' => $product['product_id'] ?? null,
                    'product_title' => $product['product_title'] ?? null,
                    'category' => $product['second_level_category_name'] ?? null,
                ];
            }, $products);

            // Use OpenAI to select the top 10 most diverse and relevant products based on user query
            $topProductsResponse = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => <<<EOT
                        You are an expert product selection AI. Your task is to analyze a list of products and select the **top 10 best matches** based on:

                        **NEVER return less than 10 products.**

                        - The user's original query
                        - A list of extracted search keywords

                        Your selection must follow these rules:

                        1. Return **EXACTLY 10 product IDs** in a JSON array called `selected_products`.

                        2. Each selected product must:
                        - Match **at least one of the extracted keywords**
                        - Be clearly related to both the **query** and the **keywords**
                        - Be different in **type or category** from the other selections (no duplicates or close alternatives)

                        3. If the query or keywords mention things like **gift**, **outfit**, or **ideas**:
                        - Ensure a variety of product types (not 5 aprons or 3 accessories)

                        4. **DO NOT** include products that do not match any keyword.
                        5. Only return a valid JSON object — no text, no explanations, no reasons.

                        6- If the user asks for a "smartwatch", ALL products must be smartwatches not anything else like case or charger

                        **NEVER return less than 10 products.**

                        Output format:
                        {
                        "selected_products": [123, 456, 789, 101, 202, 303, 404, 505, 606, 707]
                        }

                        **NEVER return less than 10 products.**

                        EOT

                    ],
                    [
                        'role' => 'user',
                        'content' => "User query: '{$userPrompt}'\n\nKeywords: {$keywordsString}\n\nAvailable products: " . json_encode($result)
                    ],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            $recommendations = json_decode($topProductsResponse->choices[0]->message->content, true);

            // Get the selected product IDs
            $selectedProductIds = $recommendations['selected_products'] ?? [];
            $reasons = $recommendations['reasons'] ?? [];

            // Find the full product details for the selected products
            $topProducts = [];
            $reasonsMap = [];

            foreach ($products as $index => $product) {
                $productId = $product['product_id'];
                $position = array_search($productId, $selectedProductIds);

                if ($position !== false) {
                    $topProducts[] = $product;
                    $reasonsMap[$productId] = $reasons[$position] ?? 'Recommended based on your search';
                }

                // If we have all 10 products, break
                if (count($topProducts) >= 10) {
                    break;
                }
            }

            // Save the recommended products to database and increment recommendation counts
            foreach ($topProducts as $productData) {
                try {
                    AliExpressProduct::createOrUpdateFromApiResponse($productData);
                    Log::info('Saved AliExpress product to database:', [
                        'product_id' => $productData['product_id'] ?? 'unknown',
                        'title' => $productData['product_title'] ?? 'unknown'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to save AliExpress product to database:', [
                        'product_id' => $productData['product_id'] ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Return products with details, without the friendly message
            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $topProducts,
                    'reasons' => $reasonsMap,
                    'user_query' => $userPrompt,
                    'extracted_keywords' => $extractedKeywords,
                    'category_names' => $categoryNames,
                    'category_ids' => $categoryIds,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('AliExpress product recommendation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all categories from AliExpress API
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getCategories()
    {
        try {
            // Call the helper function to get all categories
            $categoriesResponse = AliExpressCategories();

            // Check for errors
            if (isset($categoriesResponse['error']) && $categoriesResponse['error'] === true) {
                return response()->json([
                    'success' => false,
                    'message' => $categoriesResponse['message'] ?? 'Failed to retrieve categories',
                    'code' => $categoriesResponse['code'] ?? 500
                ], 500);
            }

            // Extract only category name and ID from the response
            $simplifiedCategories = [];
            if (isset($categoriesResponse['aliexpress_affiliate_category_get_response']['resp_result']['result']['categories']['category'])) {
                $categories = $categoriesResponse['aliexpress_affiliate_category_get_response']['resp_result']['result']['categories']['category'];
                foreach ($categories as $category) {
                    $simplifiedCategories[] = [
                        'id' => $category['category_id'] ?? null,
                        'name' => $category['category_name'] ?? null
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $simplifiedCategories
            ]);
        } catch (\Exception $e) {
            Log::error('AliExpress categories error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
