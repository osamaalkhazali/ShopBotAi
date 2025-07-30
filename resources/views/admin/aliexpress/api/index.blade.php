@extends('admin.layouts.admin')

@section('title', 'AliExpress API Test')

@section('breadcrumbs')
    <span class="text-gray-500">AliExpress</span>
    <span class="mx-2">/</span>
    <span class="text-gray-900">API Test</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">AliExpress API Test</h1>
            <p class="text-gray-600 text-sm mt-1">Test AliExpress API functionality and monitor performance</p>
        </div>
        <button id="refresh-status" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center text-sm">
            <i class="fas fa-sync-alt mr-2"></i>
            Refresh Status
        </button>
    </div>

    <!-- API Status Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">API Configuration Status</h2>
        <div id="api-status-content">
            <div class="animate-pulse">
                <div class="h-4 bg-gray-200 rounded w-1/4 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>
    </div>

    <!-- Helper Function Tests -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- AliExpressProducts Helper Test -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Helper: AliExpressProducts</h2>
            <p class="text-gray-600 text-sm mb-4">Test the AliExpressProducts helper function from helpers.php</p>
            <div class="space-y-3">
                <!-- Required Fields -->
                <input type="text"
                       id="helper-products-keywords"
                       placeholder="Enter keywords (e.g., 'phone case')"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       value="phone case">

                <!-- Basic Search Parameters -->
                <div class="grid grid-cols-2 gap-3">
                    <input type="text"
                           id="helper-products-category"
                           placeholder="Category IDs (optional)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="number"
                           id="helper-products-page-size"
                           placeholder="Page size (1-50)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           value="5"
                           min="1"
                           max="50">
                </div>

                <!-- Price Range -->
                <div class="grid grid-cols-2 gap-3">
                    <input type="number"
                           id="helper-products-min-price"
                           placeholder="Min Price (USD)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           min="0"
                           step="0.01">
                    <input type="number"
                           id="helper-products-max-price"
                           placeholder="Max Price (USD)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           min="0"
                           step="0.01">
                </div>

                <!-- Page Number and Sort -->
                <div class="grid grid-cols-2 gap-3">
                    <input type="number"
                           id="helper-products-page-no"
                           placeholder="Page Number"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           value="1"
                           min="1">
                    <select id="helper-products-sort"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Default Sort</option>
                        <option value="SALE_PRICE_ASC">Price: Low to High</option>
                        <option value="SALE_PRICE_DESC">Price: High to Low</option>
                        <option value="DISCOUNT_ASC">Discount: Low to High</option>
                        <option value="DISCOUNT_DESC">Discount: High to Low</option>
                        <option value="LAST_VOLUME_ASC">Sales: Low to High</option>
                        <option value="LAST_VOLUME_DESC">Sales: High to Low</option>
                    </select>
                </div>

                <!-- Language and Currency -->
                <div class="grid grid-cols-2 gap-3">
                    <select id="helper-products-language"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="EN">English</option>
                        <option value="RU">Russian</option>
                        <option value="PT">Portuguese</option>
                        <option value="ES">Spanish</option>
                        <option value="FR">French</option>
                        <option value="ID">Indonesian</option>
                        <option value="IT">Italian</option>
                        <option value="TH">Thai</option>
                        <option value="JA">Japanese</option>
                        <option value="AR">Arabic</option>
                        <option value="VI">Vietnamese</option>
                        <option value="TR">Turkish</option>
                        <option value="DE">German</option>
                        <option value="HE">Hebrew</option>
                        <option value="KO">Korean</option>
                        <option value="NL">Dutch</option>
                        <option value="PL">Polish</option>
                        <option value="MX">Mexican Spanish</option>
                        <option value="CL">Chilean Spanish</option>
                        <option value="IN">Hindi</option>
                    </select>
                    <select id="helper-products-currency"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="USD">USD</option>
                        <option value="GBP">GBP</option>
                        <option value="CAD">CAD</option>
                        <option value="EUR">EUR</option>
                        <option value="AUD">AUD</option>
                        <option value="RUB">RUB</option>
                        <option value="BRL">BRL</option>
                        <option value="INR">INR</option>
                        <option value="JPY">JPY</option>
                        <option value="IDR">IDR</option>
                        <option value="SEK">SEK</option>
                    </select>
                </div>

                <!-- Ship to Country and Fields -->
                <div class="grid grid-cols-2 gap-3">
                    <input type="text"
                           id="helper-products-ship-to"
                           placeholder="Ship to Country (e.g., US)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="text"
                           id="helper-products-fields"
                           placeholder="Fields (comma separated)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           value="commission_rate,sale_price">
                </div>

                <button id="test-helper-products" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg transition-colors flex items-center justify-center">
                    <i class="fas fa-code mr-2"></i>
                    Test Helper Products
                </button>
            </div>
            <div id="helper-products-result" class="mt-4 hidden"></div>
        </div>

        <!-- AliExpressCategories Helper Test -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Helper: AliExpressCategories</h2>
            <p class="text-gray-600 text-sm mb-4">Test the AliExpressCategories helper function from helpers.php</p>
            <button id="test-helper-categories" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-lg transition-colors flex items-center justify-center">
                <i class="fas fa-tags mr-2"></i>
                Test Helper Categories
            </button>
            <div id="helper-categories-result" class="mt-4 hidden"></div>
        </div>
    </div>

    <!-- Test Results History -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Test Results</h2>
        <div id="test-history" class="space-y-4">
            <p class="text-gray-500 text-sm">No tests performed yet. Run a test to see results here.</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load API status on page load
    loadApiStatus();

    // Event listeners
    document.getElementById('refresh-status').addEventListener('click', loadApiStatus);
    document.getElementById('test-helper-products').addEventListener('click', testHelperProducts);
    document.getElementById('test-helper-categories').addEventListener('click', testHelperCategories);

    function loadApiStatus() {
        const content = document.getElementById('api-status-content');
        content.innerHTML = '<div class="animate-pulse"><div class="h-4 bg-gray-200 rounded w-1/4 mb-2"></div><div class="h-4 bg-gray-200 rounded w-1/2"></div></div>';

        fetch('{{ route("admin.aliexpress.api.status") }}')
            .then(response => response.json())
            .then(data => {
                content.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600">API Key Status</p>
                            <p class="text-lg ${data.api_key_configured ? 'text-green-600' : 'text-red-600'}">
                                <i class="fas fa-${data.api_key_configured ? 'check-circle' : 'times-circle'} mr-2"></i>
                                ${data.api_key_configured ? 'Configured' : 'Not Configured'}
                            </p>
                            ${data.api_key_configured ? `<p class="text-sm text-gray-500">Key: ${data.api_key_masked}</p>` : ''}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Base URL</p>
                            <p class="text-sm text-gray-900">${data.base_url}</p>
                            <p class="text-sm text-gray-500">Engine: ${data.engine}</p>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                content.innerHTML = `<p class="text-red-600"><i class="fas fa-exclamation-triangle mr-2"></i>Error loading API status</p>`;
            });
    }

    // Helper test functions
    function testHelperProducts() {
        const button = document.getElementById('test-helper-products');
        const result = document.getElementById('helper-products-result');

        const keywords = document.getElementById('helper-products-keywords').value;
        const category = document.getElementById('helper-products-category').value;
        const pageSize = document.getElementById('helper-products-page-size').value;
        const minPrice = document.getElementById('helper-products-min-price').value;
        const maxPrice = document.getElementById('helper-products-max-price').value;
        const pageNo = document.getElementById('helper-products-page-no').value;
        const sort = document.getElementById('helper-products-sort').value;
        const language = document.getElementById('helper-products-language').value;
        const currency = document.getElementById('helper-products-currency').value;
        const shipTo = document.getElementById('helper-products-ship-to').value;
        const fields = document.getElementById('helper-products-fields').value;

        if (!keywords.trim()) {
            alert('Please enter keywords for search');
            return;
        }

        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';
        result.classList.add('hidden');

        // Prepare parameters object
        const params = {
            keywords: keywords,
            page_size: pageSize || '5',
            page_no: pageNo || '1',
            target_language: language || 'EN',
            target_currency: currency || 'USD'
        };

        // Add optional parameters only if they have values
        if (category) params.category_ids = category;
        if (minPrice) params.min_sale_price = minPrice;
        if (maxPrice) params.max_sale_price = maxPrice;
        if (sort) params.sort = sort;
        if (shipTo) params.ship_to_country = shipTo;
        if (fields) params.fields = fields;

        fetch('{{ route("admin.aliexpress.api.test-helper-products") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(params)
        })
        .then(response => response.json())
        .then(data => {
            showHelperResult(result, data, 'AliExpressProducts Helper Test');
            addToHistory('AliExpressProducts Helper Test', data, params);
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-code mr-2"></i>Test Helper Products';
        });
    }

    function testHelperCategories() {
        const button = document.getElementById('test-helper-categories');
        const result = document.getElementById('helper-categories-result');

        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';
        result.classList.add('hidden');

        fetch('{{ route("admin.aliexpress.api.test-helper-categories") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            showHelperResult(result, data, 'AliExpressCategories Helper Test');
            addToHistory('AliExpressCategories Helper Test', data);
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-tags mr-2"></i>Test Helper Categories';
        });
    }

    function showHelperResult(container, data, testType) {
        const statusClass = data.success ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50';
        const iconClass = data.success ? 'text-green-600 fa-check-circle' : 'text-red-600 fa-times-circle';

        let paramsHtml = '';
        if (data.params) {
            paramsHtml = '<div class="mt-2 mb-3"><strong>Parameters:</strong><pre class="mt-1 text-xs bg-gray-100 p-2 rounded overflow-x-auto">' +
                         JSON.stringify(data.params, null, 2) + '</pre></div>';
        }

        let sampleHtml = '';
        if (data.sample_products) {
            sampleHtml = '<div class="mt-3"><strong>Sample Products:</strong><pre class="mt-1 text-xs bg-gray-100 p-2 rounded overflow-x-auto max-h-60">' +
                         JSON.stringify(data.sample_products, null, 2) + '</pre></div>';
        } else if (data.sample_categories) {
            sampleHtml = '<div class="mt-3"><strong>Sample Categories:</strong><pre class="mt-1 text-xs bg-gray-100 p-2 rounded overflow-x-auto max-h-60">' +
                         JSON.stringify(data.sample_categories, null, 2) + '</pre></div>';
        }

        container.innerHTML = `
            <div class="border ${statusClass} rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <i class="fas ${iconClass} mr-2"></i>
                    <span class="font-medium">${testType} ${data.success ? 'Successful' : 'Failed'}</span>
                </div>
                <div class="text-sm space-y-1">
                    <p>${data.message || 'No message provided'}</p>
                    ${data.response_time ? `<p><strong>Response Time:</strong> ${data.response_time}</p>` : ''}
                    ${data.products_returned !== undefined ? `<p><strong>Products Found:</strong> ${data.products_returned} of ${data.total_results}</p>` : ''}
                    ${data.categories_count !== undefined ? `<p><strong>Categories Found:</strong> ${data.categories_count}</p>` : ''}
                    ${data.error ? `<p class="text-red-600"><strong>Error:</strong> ${data.error}</p>` : ''}
                    ${paramsHtml}
                    ${sampleHtml}
                </div>
            </div>
        `;
        container.classList.remove('hidden');
    }

    function addToHistory(testType, data, extra = {}) {
        const historyContainer = document.getElementById('test-history');
        const timestamp = new Date().toLocaleString();

        if (historyContainer.querySelector('p')) {
            historyContainer.innerHTML = '';
        }

        const statusClass = data.success ? 'border-l-green-500 bg-green-50' : 'border-l-red-500 bg-red-50';
        const iconClass = data.success ? 'text-green-600 fa-check-circle' : 'text-red-600 fa-times-circle';

        const historyItem = document.createElement('div');
        historyItem.className = `border-l-4 ${statusClass} p-4 rounded`;
        historyItem.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas ${iconClass} mr-2"></i>
                    <span class="font-medium">${testType}</span>
                    ${extra.query ? `<span class="ml-2 text-sm text-gray-600">"${extra.query}"</span>` : ''}
                </div>
                <span class="text-xs text-gray-500">${timestamp}</span>
            </div>
            <div class="text-sm text-gray-600 mt-1">
                Status: ${data.status_code} | ${data.message || (data.success ? 'Success' : 'Failed')}
            </div>
        `;

        historyContainer.insertBefore(historyItem, historyContainer.firstChild);

        // Keep only last 10 items
        while (historyContainer.children.length > 10) {
            historyContainer.removeChild(historyContainer.lastChild);
        }
    }
});
</script>
@endsection
