<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AliExpressProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'aliexpress_products';

    protected $fillable = [
        'product_id',
        'product_title',
        'app_sale_price',
        'original_price',
        'sale_price',
        'target_sale_price',
        'target_original_price',
        'target_app_sale_price',
        'app_sale_price_currency',
        'original_price_currency',
        'sale_price_currency',
        'target_sale_price_currency',
        'target_original_price_currency',
        'target_app_sale_price_currency',
        'discount',
        'tax_rate',
        'product_detail_url',
        'product_main_image_url',
        'product_small_image_urls',
        'product_video_url',
        'promotion_link',
        'sku_id',
        'first_level_category_name',
        'first_level_category_id',
        'second_level_category_name',
        'second_level_category_id',
        'shop_name',
        'shop_id',
        'shop_url',
        'commission_rate',
        'hot_product_commission_rate',
        'latest_volume',
        'recommendation_count'
    ];

    protected $casts = [
        'product_small_image_urls' => 'array',
        'app_sale_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'target_sale_price' => 'decimal:2',
        'target_original_price' => 'decimal:2',
        'target_app_sale_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'recommendation_count' => 'integer',
        'latest_volume' => 'integer',
        'product_id' => 'integer',
        'sku_id' => 'integer',
        'shop_id' => 'integer',
        'first_level_category_id' => 'integer',
        'second_level_category_id' => 'integer',
    ];

    /**
     * Static method to create or update a product from AliExpress API response
     */
    public static function createOrUpdateFromApiResponse(array $productData): self
    {
        $product = self::updateOrCreate(
            ['product_id' => $productData['product_id']],
            [
                'product_title' => $productData['product_title'],
                'app_sale_price' => $productData['app_sale_price'],
                'original_price' => $productData['original_price'],
                'sale_price' => $productData['sale_price'],
                'target_sale_price' => $productData['target_sale_price'] ?? null,
                'target_original_price' => $productData['target_original_price'] ?? null,
                'target_app_sale_price' => $productData['target_app_sale_price'] ?? null,
                'app_sale_price_currency' => $productData['app_sale_price_currency'] ?? 'CNY',
                'original_price_currency' => $productData['original_price_currency'] ?? 'CNY',
                'sale_price_currency' => $productData['sale_price_currency'] ?? 'CNY',
                'target_sale_price_currency' => $productData['target_sale_price_currency'] ?? 'USD',
                'target_original_price_currency' => $productData['target_original_price_currency'] ?? 'USD',
                'target_app_sale_price_currency' => $productData['target_app_sale_price_currency'] ?? 'USD',
                'discount' => $productData['discount'] ?? null,
                'tax_rate' => $productData['tax_rate'] ?? 0.00,
                'product_detail_url' => $productData['product_detail_url'],
                'product_main_image_url' => $productData['product_main_image_url'],
                'product_small_image_urls' => $productData['product_small_image_urls']['string'] ?? null,
                'product_video_url' => $productData['product_video_url'] ?? null,
                'promotion_link' => $productData['promotion_link'],
                'sku_id' => $productData['sku_id'],
                'first_level_category_name' => $productData['first_level_category_name'],
                'first_level_category_id' => $productData['first_level_category_id'],
                'second_level_category_name' => $productData['second_level_category_name'],
                'second_level_category_id' => $productData['second_level_category_id'],
                'shop_name' => $productData['shop_name'],
                'shop_id' => $productData['shop_id'],
                'shop_url' => $productData['shop_url'],
                'commission_rate' => $productData['commission_rate'] ?? '0.0%',
                'hot_product_commission_rate' => $productData['hot_product_commission_rate'] ?? '0.0%',
                'latest_volume' => $productData['lastest_volume'] ?? 0,
                'recommendation_count' => 1
            ]
        );

        // If product already exists, increment recommendation count
        if (!$product->wasRecentlyCreated) {
            $product->increment('recommendation_count');
        }

        return $product;
    }

    /**
     * Get saved products for this AliExpress product
     */
    public function aliexpressSavedProducts()
    {
        return $this->hasMany(AliExpressSavedProduct::class, 'aliexpress_product_id');
    }

    /**
     * Get viewed products for this AliExpress product
     */
    public function aliexpressViewedProducts()
    {
        return $this->hasMany(AliExpressViewedProduct::class, 'aliexpress_product_id');
    }

    /**
     * Scope to get most recommended products
     */
    public function scopeMostRecommended($query, $limit = 10)
    {
        return $query->orderBy('recommendation_count', 'desc')->limit($limit);
    }

    /**
     * Scope to get products by category
     */
    public function scopeByCategory($query, $categoryId, $level = 'first')
    {
        $column = $level === 'first' ? 'first_level_category_id' : 'second_level_category_id';
        return $query->where($column, $categoryId);
    }

    /**
     * Scope to get products in price range
     */
    public function scopeInPriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('target_sale_price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('target_sale_price', '<=', $maxPrice);
        }
        return $query;
    }

    /**
     * Get the searchable array for Scout
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_title' => $this->product_title,
            'first_level_category_name' => $this->first_level_category_name,
            'second_level_category_name' => $this->second_level_category_name,
            'shop_name' => $this->shop_name,
            'target_sale_price' => $this->target_sale_price,
        ];
    }
}
