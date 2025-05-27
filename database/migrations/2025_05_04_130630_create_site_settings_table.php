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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('ShopBot');
            $table->string('site_logo')->nullable();
            $table->string(column: 'favicon')->nullable();


            // Main brand colors
            $table->string('primary_color')->default('#5a4fcf');
            $table->string('primary_dark_color')->default('#4a3fb8');
            $table->string('primary_light_color')->default('#7b71e3');

            // Secondary colors
            $table->string('secondary_color')->default('#1e293b');
            $table->string('secondary_dark_color')->default('#0f172a');
            $table->string('secondary_light_color')->default('#334155');

            // Accent colors
            $table->string('accent_color')->default('#f97316');

            // Background colors
            $table->string('bg_dark_color')->default('#111827');
            $table->string('bg_medium_color')->default('#1f2937');
            $table->string('bg_light_color')->default('#374151');

            // Status colors
            $table->string('success_color')->default('#10b981');
            $table->string('warning_color')->default('#f59e0b');
            $table->string('error_color')->default('#ef4444');

            // Text colors
            $table->string('text_light_color')->default('#f6f8fd');
            $table->string('text_dark_color')->default('#111827');

            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
