<?php

use Carbon\Carbon;
// Directly include all needed SDK classes
require_once __DIR__ . '/../../AliExpressSDK/IopSdk.php';
require_once __DIR__ . '/../../AliExpressSDK/iop/IopClient.php';
require_once __DIR__ . '/../../AliExpressSDK/iop/IopRequest.php';
require_once __DIR__ . '/../../AliExpressSDK/iop/Constants.php';
require_once __DIR__ . '/../../AliExpressSDK/iop/IopLogger.php';

/**
 * Write code on Method
 *
 * @return response()
 */
if (! function_exists('convertYmdToMdy')) {
    function AliExpressProducts($params = [])
    {
        try {
            // Create client for AliExpress API
            $c = new IopClient('https://api-sg.aliexpress.com/sync', '514864', 'MX9wpA5ukYeWkzbz9Kp4xOPZX0fTC62W');
            $request = new IopRequest('aliexpress.affiliate.product.query');
            // Set API parameters for product search
            $request->addApiParam('app_signature', 'ShopBot AI');
            $request->addApiParam('category_ids', $params['category_ids'] ?? '');
            $request->addApiParam('fields', $params['fields'] ?? '');
            $request->addApiParam('keywords', $params['keywords'] ?? 'black,shoes');
            $request->addApiParam('max_sale_price', $params['max_sale_price'] ?? '1000000000');
            $request->addApiParam('min_sale_price', $params['min_sale_price'] ?? '0');
            $request->addApiParam('page_no', $params['page_no'] ?? '1');
            $request->addApiParam('page_size', $params['page_size'] ?? '10');
            $request->addApiParam('platform_product_type', 'ALL');
            $request->addApiParam('sort', $params['sort'] ?? '');
            $request->addApiParam('target_currency', $params['target_currency'] ?? 'USD');
            $request->addApiParam('target_language', $params['target_language'] ?? 'EN');
            $request->addApiParam('ship_to_country', $params['ship_to_country'] ?? '');
            $request->addApiParam('promotion_name', 'ShopBot AI');


            // dd($request->udfParams);
            // Execute with timeout set in the IopClient class
            $response = $c->execute($request);


            // Return the product data
            return json_decode($response, true);
        } catch (\Exception $e) {
            return ["error" => true, "message" => $e->getMessage(), "code" => $e->getCode()];
        }
    }
}

/**
 * Get all categories from AliExpress API
 *
 * @return array
 */
if (! function_exists('AliExpressCategories')) {
    function AliExpressCategories()
    {
        try {
            // Create client for AliExpress API
            $c = new IopClient('https://api-sg.aliexpress.com/sync', '514864', 'MX9wpA5ukYeWkzbz9Kp4xOPZX0fTC62W');
            $request = new IopRequest('aliexpress.affiliate.category.get');

            // Set API parameters
            $request->addApiParam('app_signature', 'ShopBot AI');

            // Execute with timeout set in the IopClient class
            $response = $c->execute($request);

            // Return the category data
            return json_decode($response, true);
        } catch (\Exception $e) {
            return ["error" => true, "message" => $e->getMessage(), "code" => $e->getCode()];
        }
    }
}
