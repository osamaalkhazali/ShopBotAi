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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('asin')->unique();
            $table->string('title');
            $table->string('imgUrl');
            $table->string('productURL');
            $table->decimal('stars', 3, 1)->nullable();
            $table->integer('reviews')->default(0);
            $table->decimal('price', 10, 2);
            $table->decimal('listPrice', 10, 2)->nullable();
            $table->foreignId('category_id')->constrained('categories');
            $table->boolean('isBestSeller')->default(false);
            $table->integer('boughtInLastMonth')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Add indexes directly in table creation
            $table->index('price');
            $table->index('stars');
            $table->fullText('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
