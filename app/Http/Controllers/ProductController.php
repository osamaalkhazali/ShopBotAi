<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Search for products based on keywords and price
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Product::query();

        if ($request->filled('keywords')) {
            $searchTerm = trim($request->keywords);

            // Search only in the title field (no description field exists)
            $query->where(function ($q) use ($searchTerm) {
                // Exact phrase match
                $q->where('title', 'like', '%' . $searchTerm . '%');
            });

            // Then also try individual word matching but with less priority
            $keywords = preg_split('/\s+/', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);

            // Only apply word-by-word search if we have multiple words
            if (count($keywords) > 1) {
                foreach ($keywords as $word) {
                    if (strlen($word) > 2) { // Ignore very short words
                        $query->orWhere('title', 'like', '%' . $word . '%');
                    }
                }
            }
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Get top 30 most relevant results, prioritizing exact matches
        $results = $query->orderByRaw(
            "
            CASE
                WHEN title LIKE ? THEN 1
                WHEN title LIKE ? THEN 2
                ELSE 3
            END",
            [$request->keywords, '%' . $request->keywords . '%']
        )
            ->orderByDesc('stars')
            ->limit(30)
            ->get();

        return response()->json($results);
    }

    /**
     * Recommend products using OpenAI
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommendWithAI(Request $request)
    {
        try {
            // Create OpenAI client with SSL verification disabled
            $client = OpenAI::factory()
                ->withApiKey(config('services.openai.key'))
                ->withHttpClient(new \GuzzleHttp\Client([
                    'verify' => false,
                ]))
                ->make();

            $userPrompt = $request->get('keywords', ''); // full user input
            $maxPrice = $request->input('max_price'); // Only use if explicitly provided

            // Get all categories
            $categories = Category::all();
            $categoryNames = $categories->pluck('category_name')->toArray();
            $categoryListString = implode(', ', $categoryNames);

            Log::info('Starting AI recommendation process for query:', ['query' => $userPrompt]);
            Log::info('Available categories:', ['categories' => $categoryNames]);

            // Use OpenAI to extract keywords, price, gender, and select relevant categories
            $extractResponse = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are an e-commerce AI assistant with deep understanding of shopping contexts and product categorization. When given a user search query:

1. CRITICAL: Detect the language of the user's query, but ALWAYS extract keywords and categories in ENGLISH regardless of input language.
2. Extract EXACTLY 10 highly specific, individual product keywords in ENGLISH. No more, no less - you MUST provide EXACTLY 10 keywords. Focus on single words or very short phrases that precisely describe product features, types, or attributes. Prioritize broad, individual product terms over longer phrases. For example, instead of 'summer casual outfit', provide separate keywords like 'shirt', 'shorts', 'casual', 'summer'.
3. If a gender or age group (men, women, boys, girls, kids, baby) is detected, DO NOT repeat the gender words in the keywords - the gender will be used only for category selection.
4. Extract any price limits mentioned. IMPORTANT: If user mentions a price in a non-USD currency (EUR, GBP, JPY, etc.), you MUST convert it to USD using the latest approximate exchange rates. For example, if user says '100 euros', calculate and provide the USD equivalent (approximately 108 USD). Always store the price limit as a number in USD without the currency symbol.
5. Extract gender context if relevant (men, women, boys, girls, kids, baby).
6. Select the 10 most relevant categories FROM THIS LIST (ALWAYS in ENGLISH), exactly as they are written:\n\n{$categoryListString}\n\n
7. If the user's query suggests a gift or present, expand the keywords list to include popular gift-related terms (such as \"gift\", \"accessories\", \"perfume\", \"jewelry\", \"watch\", \"tech gadgets\", \"wallet\", \"handbag\", \"scarf\", \"decor\", \"beauty\", \"personal care\", \"electronics\"), even if the user did not mention them. Also, when selecting categories, do not limit only to categories directly matching the user's query. Include categories that are commonly good for gifts for the detected gender or age group.

Respond in JSON format. You MUST include AT LEAST 10 keywords in your response AND do not include words like 'gift' with them unless the user want gift tools - this is a strict requirement. Example:
User: 'I need a summer outfit for a sporty teenage boy under 50 euros.'
Response:
{
  \"keywords\": [\"shirt\", \"t-shirt\", \"shorts\", \"summer\", \"sporty\", \"athletic\", \"casual\", \"lightweight\", \"breathable\", \"comfortable\"],
  \"max_price\": 54,
  \"gender\": \"boys\",
  \"categories\": [\"Boys' Clothing\", \"Boys' Accessories\", \"Sports & Fitness\", \"T-Shirts & Tops\", \"Shorts\", \"Athletic Wear\", \"Summer Clothing\", \"Kids' Clothing\", \"Casual Wear\", \"Budget Fashion\"],
  \"detected_language\": \"english\",
  \"original_currency\": \"EUR\"
}"
                    ],
                    [
                        'role' => 'user',
                        'content' => "Extract search keywords, price limit, gender, and select the most relevant categories for: {$userPrompt}"
                    ]
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            $parsed = json_decode($extractResponse->choices[0]->message->content, true);

            // Log raw response for debugging
            Log::info('AI raw extraction response:', ['content' => $extractResponse->choices[0]->message->content]);

            $extractedKeywords = $parsed['keywords'] ?? [];
            $extractedPrice = $parsed['max_price'] ?? null;
            $extractedGender = $parsed['gender'] ?? null;
            $selectedCategories = $parsed['categories'] ?? [];
            $detectedLanguage = $parsed['detected_language'] ?? 'english';

            Log::info('Detected user language:', ['language' => $detectedLanguage]);

            // Double-check we have exactly 10 keywords, force it if needed
            if (!empty($parsed) && isset($parsed['keywords'])) {
                if (count($parsed['keywords']) < 10) {
                    // If fewer than 10 keywords, duplicate some to reach 10
                    Log::warning('AI returned fewer than 10 keywords, padding list:', ['original_count' => count($parsed['keywords'])]);

                    $originalKeywords = $parsed['keywords'];
                    while (count($parsed['keywords']) < 10) {
                        // Add keywords from the beginning of the list until we have 10
                        $parsed['keywords'][] = $originalKeywords[count($parsed['keywords']) % count($originalKeywords)];
                    }
                } else if (count($parsed['keywords']) > 10) {
                    // If more than 10 keywords, trim to 10
                    Log::warning('AI returned more than 10 keywords, trimming list:', ['original_count' => count($parsed['keywords'])]);
                    $parsed['keywords'] = array_slice($parsed['keywords'], 0, 10);
                }

                // Update extracted keywords with our fixed list
                $extractedKeywords = $parsed['keywords'];
                Log::info('Final keyword list (exactly 10):', $extractedKeywords);
            }

            // Use extracted price if no explicit max_price was provided
            if (!$maxPrice && $extractedPrice) {
                $maxPrice = $extractedPrice;
            }

            // Updated fallback to use the full user prompt as one keyword
            if (empty($extractedKeywords)) {
                Log::warning('No keywords extracted by AI, using fallback to full user prompt');
                $extractedKeywords = [trim($userPrompt)]; // whole phrase as one keyword
            } else if (count($extractedKeywords) < 5) {
                // If AI returned fewer than 5 keywords, log a warning
                Log::warning('AI returned fewer than 5 keywords:', ['count' => count($extractedKeywords), 'keywords' => $extractedKeywords]);
            }

            // Log extracted information for debugging
            Log::info('AI extracted keywords:', $extractedKeywords);
            Log::info('AI extracted price:', [$extractedPrice]);
            Log::info('AI extracted gender:', [$extractedGender]);
            Log::info('AI selected categories:', $selectedCategories);

            // Build query using AI-extracted information
            $query = Product::query();

            // Apply category filter based on AI-selected categories
            if (!empty($selectedCategories)) {
                $categoryIds = $categories->filter(function ($category) use ($selectedCategories) {
                    return in_array($category->category_name, $selectedCategories);
                })->pluck('id')->toArray();

                if (!empty($categoryIds)) {
                    $query->whereIn('category_id', $categoryIds);
                    Log::info('Filtering by category IDs:', ['category_ids' => $categoryIds]);
                }
            }

            // Apply price filter if provided
            if ($maxPrice) {
                $query->where(function ($q) use ($maxPrice) {
                    $q->where('price', '<=', $maxPrice)
                        ->orWhereNull('price');
                });
                Log::info('Applied price filter:', ['max_price' => $maxPrice]);
            }

            // Apply keyword filtering - ONLY search in title field (no description field exists)
            if (!empty($extractedKeywords)) {
                $query->where(function ($q) use ($extractedKeywords) {
                    foreach ($extractedKeywords as $keyword) {
                        if (strlen(trim($keyword)) > 0) {
                            // Escape quotes in keywords to prevent SQL injection
                            $safeKeyword = str_replace("'", "''", $keyword);
                            $q->orWhere('title', 'like', '%' . $safeKeyword . '%');
                        }
                    }
                });
            }

            // Add smart ordering based on relevance
            $orderClauses = [];
            foreach ($extractedKeywords as $keyword) {
                if (strlen(trim($keyword)) > 0) {
                    $safeKeyword = str_replace("'", "''", $keyword);
                    $orderClauses[] = "CASE WHEN title LIKE '%$safeKeyword%' THEN 1 ELSE 0 END";
                }
            }

            if (!empty($orderClauses)) {
                // Create a combined relevance score and order by it
                $query->orderByRaw(implode(' + ', $orderClauses) . ' DESC');
            }

            // Add randomized ordering to ensure variety in results
            $query->orderByRaw('RAND()');

            // Get a reasonable number of products
            $products = $query->limit(100)->get();

            // If no products found with current filters, try a broader search
            if ($products->isEmpty()) {
                Log::info('No products found with strict filters, trying broader search');

                $query = Product::query();

                // Apply only price filter if provided
                if ($maxPrice) {
                    $query->where('price', '<=', $maxPrice);
                }

                // Use a broader keyword match
                if (!empty($extractedKeywords)) {
                    $query->where(function ($q) use ($extractedKeywords) {
                        foreach ($extractedKeywords as $keyword) {
                            if (strlen(trim($keyword)) > 0) {
                                $q->orWhere('title', 'like', '%' . str_replace("'", "''", $keyword) . '%');
                            }
                        }
                    });
                }
                // orderByDesc('stars')->
                $products = $query->limit(50)->get();
            }

            // If still no products found, return early
            if ($products->isEmpty()) {
                return response()->json([
                    'ai_reply' => 'No products found matching your criteria.',
                    'raw_products' => [],
                    'extracted_keywords' => $extractedKeywords,
                    'extracted_price' => $extractedPrice ? "Under $" . number_format($extractedPrice, 2) : null,
                ]);
            }

            // Format products for OpenAI - Use string concatenation instead of repeated array operations
            $productList = "";
            foreach ($products as $index => $product) {
                $category = $product->category ? $product->category->category_name : 'Uncategorized';
                $formattedPrice = $product->price ?? ($product->listPrice ?? 'Price not available');
                $stars = $product->stars ?? 0;
                $reviews = $product->reviews ?? 0;
                $productList .= ($index + 1) . ". {$product->title} - {$formattedPrice} - Rating: {$stars}/5 ({$reviews} reviews) - Category: {$category}\n";
            }

            // Generate a system prompt for product recommendations
            $systemPrompt = "You are an expert e-commerce consultant with deep knowledge about what people need for various activities and occasions. Act like a friendly, helpful shopping assistant. ";

            // Add gender-specific guidance if gender was detected
            if ($extractedGender) {
                $systemPrompt .= "You specialize in recommending products for {$extractedGender}. ";
            }

            // Get original currency info if available
            $originalCurrency = $parsed['original_currency'] ?? null;
            $currencyInfo = '';
            if ($originalCurrency && $extractedPrice) {
                $currencyInfo = " The user originally specified a price in {$originalCurrency} which has been converted to {$extractedPrice} USD for product filtering.";
            }

            // Add language-specific instruction
            $systemPrompt .= "CRITICALLY IMPORTANT: The user is writing in '{$detectedLanguage}' language. You MUST respond in the SAME LANGUAGE as the user's query.{$currencyInfo}\n\n";

            $systemPrompt .= "For all recommendations:
1. Ensure items are appropriate for the specific context mentioned in the user's request
2. Include a diverse selection of products that address different aspects of their needs
3. Consider quality, ratings, and reviews when selecting products
4. Provide clear, specific reasons why each product would be beneficial for the user
5. When possible, recommend complementary products that work well together

Select products that would genuinely help the user with their specific needs based on the context they've provided.

Format the response as JSON with two properties:
- recommendations: (array as before)
- message: (string with the friendly, personalized HTML-formatted message for the user)

Provide a personalized HTML-formatted message that:
1. MUST be written in {$detectedLanguage} language. This is required - do not write in English unless the user's original query was in English.
2. Starts with a friendly, funny, human-like greeting that relates to their specific query (different for sports, fashion, tech, etc).
3. ALWAYS list 3-5 product TYPES you selected (e.g., 'shirts', 'shorts', 'sneakers') in <ul> with <li> and <strong> tags.
   CRITICAL: NEVER list any actual product names or titles. You MUST ONLY mention general product types such as 'shorts', 'sneakers', 'hats', etc.
   If you mention any specific product name, it will be considered an error.
   Even though you are shown a list of products, IGNORE the full product titles and ONLY extract the general type of each product. Do not duplicate product types or similar items types.
4. ALWAYS includes another <ul> with 4-6 alternative product types the user might want to search for next.
5. 🚨 IMPORTANT: If this is a gift search (either because the user asked for a gift or your categories suggest gifting), you MUST — no exceptions — ask these follow-up questions at the end of the message, even if they were already asked before:
   <p class=\"question\">What's the recipient's age?</p>
   <p class=\"question\">What hobbies or interests do they have?</p>
   <p class=\"question\">Do they prefer any particular colors or styles?</p>
   These questions should be translated appropriately into the user's language.
6. 🚨 IMPORTANT: If this is a fashion/outfit search, you MUST — no exceptions — ask these questions:
   <p class=\"question\">Do you prefer casual or formal looks?</p>
   <p class=\"question\">Any favorite colors or styles?</p>
   These questions should be translated appropriately into the user's language.
7. ALWAYS end the message with: <p class=\"cta\">Let me know if you'd like more suggestions or something different!</p> (translated into user's language)
8. Use proper HTML tags <p>, <ul>, <li>, <strong>, <em>, and <span class=\"highlight\"> for important words or concepts.
9. Avoid generic phrases like 'Here are your recommendations' or 'Based on your search' - be conversational and friendly.
10. Write as if you're a helpful, enthusiastic shopping assistant talking directly to the user in their own language, while keeping all product types and categories in ENGLISH.

";

            // Final AI call to recommend products based on filtered list
            $response = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    ['role' => 'user', 'content' =>
                    "User is looking for: \"{$userPrompt}\".\n\nHere are some products:\n " .
                        $productList .
                        "\n\nPlease pick the BEST 10 MATCHES - you MUST select exactly 10 products to help the user with their needs. Make sure to include a diverse selection covering different aspects of their requirements. Format as JSON with 'recommendations' array containing objects with 'product_index' (integer) and 'reason' (string), and a 'message' string that gives a friendly, helpful message to the user. The reason should explain specifically why this product is good for the user's needs."]
                ],
                'response_format' => [
                    'type' => 'json_object',
                ],
            ]);

            $reply = json_decode($response->choices[0]->message->content, true);

            // Extract the AI message
            $aiMessage = $reply['message'] ?? 'Here are some product recommendations based on your search!';

            // Extract the recommended products more efficiently
            $recommendedProducts = [];
            if (isset($reply['recommendations']) && is_array($reply['recommendations'])) {
                // Create a lookup map for products by index
                $productMap = $products->mapWithKeys(function ($item, $key) {
                    return [$key + 1 => $item];
                });

                // Process all recommendations at once up to maximum of 10
                foreach ($reply['recommendations'] as $rec) {
                    if (isset($rec['product_index']) && isset($productMap[$rec['product_index']])) {
                        $recommendedProducts[] = [
                            'product' => $productMap[$rec['product_index']],
                            'reason' => $rec['reason'] ?? 'Recommended for you'
                        ];
                    }
                }
            }

            // If we didn't get enough recommendations (less than 10), add random products from the list to reach 10
            if (count($recommendedProducts) < 10 && $products->count() >= 10) {
                // Get the indexes of products already recommended
                $recommendedIndexes = collect($recommendedProducts)->map(function ($item) {
                    return $item['product']->id;
                })->toArray();

                // Filter products not yet recommended and add them until we have 10
                $additionalProducts = $products->filter(function ($product) use ($recommendedIndexes) {
                    return !in_array($product->id, $recommendedIndexes);
                })->take(10 - count($recommendedProducts));

                foreach ($additionalProducts as $product) {
                    $recommendedProducts[] = [
                        'product' => $product,
                        'reason' => 'Additional recommendation that might match your needs'
                    ];
                }
            }

            return response()->json([
                'recommendations' => $recommendedProducts,
                'message' => $aiMessage,
                'raw_products' => $products,
                'ai_response' => $reply,
                'extracted_keywords' => $extractedKeywords,
                'selected_categories' => $selectedCategories,
                'extracted_gender' => $extractedGender,
                'extracted_price' => $extractedPrice ? "Under $" . number_format($extractedPrice, 2) : null,
            ]);
        } catch (\Exception $e) {
            Log::error('AI recommendation error: ' . $e->getMessage());
            return response()->json([
                'error' => 'AI recommendation failed: ' . $e->getMessage(),
                'raw_products' => [],
            ], 500);
        }
    }
}
