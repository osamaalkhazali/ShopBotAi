<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('aliexpress_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unique(); // AliExpress product ID
            $table->string('product_title', 500);
            $table->decimal('app_sale_price', 10, 2);
            $table->decimal('original_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->decimal('target_sale_price', 10, 2)->nullable();
            $table->decimal('target_original_price', 10, 2)->nullable();
            $table->decimal('target_app_sale_price', 10, 2)->nullable();
            $table->string('app_sale_price_currency', 5)->default('CNY');
            $table->string('original_price_currency', 5)->default('CNY');
            $table->string('sale_price_currency', 5)->default('CNY');
            $table->string('target_sale_price_currency', 5)->default('USD');
            $table->string('target_original_price_currency', 5)->default('USD');
            $table->string('target_app_sale_price_currency', 5)->default('USD');
            $table->string('discount', 10)->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0.00);
            $table->string('product_detail_url', 1000);
            $table->string('product_main_image_url', 500);
            $table->json('product_small_image_urls')->nullable();
            $table->string('product_video_url', 500)->nullable();
            $table->string('promotion_link', 2000);
            $table->bigInteger('sku_id');
            $table->string('first_level_category_name', 100)->nullable();
            $table->bigInteger('first_level_category_id')->nullable();
            $table->string('second_level_category_name', 100)->nullable();
            $table->bigInteger('second_level_category_id')->nullable();
            $table->string('shop_name', 200)->nullable();
            $table->bigInteger('shop_id')->nullable();
            $table->string('shop_url', 500)->nullable();
            $table->string('commission_rate', 10)->default('0.0%');
            $table->string('hot_product_commission_rate', 10)->default('0.0%');
            $table->integer('latest_volume')->default(0);
            $table->integer('recommendation_count')->default(1); // How many times this product was recommended
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('product_id');
            $table->index('first_level_category_id');
            $table->index('second_level_category_id');
            $table->index('shop_id');
            $table->index('target_sale_price');
            $table->index('recommendation_count');
            $table->index(['first_level_category_id', 'target_sale_price'], 'aliexpress_category_price_idx');
            $table->fullText('product_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aliexpress_products');
    }
};
