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
        Schema::create('aliexpress_saved_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('aliexpress_product_id')->constrained('aliexpress_products')->onDelete('cascade');
            $table->timestamps();

            // Create a unique index to prevent duplicate saved products
            $table->unique(['user_id', 'aliexpress_product_id']);

            // Index for quick retrieval of user's saved products
            $table->index(['user_id', 'aliexpress_product_id', 'created_at'], 'aliexpress_saved_user_product_time_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aliexpress_saved_products');
    }
};
