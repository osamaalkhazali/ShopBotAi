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
    Schema::create('aliexpress_chat_messages', function (Blueprint $table) {
      $table->id();
      $table->foreignId('session_id')->constrained('aliexpress_chat_sessions')->onDelete('cascade');
      $table->enum('sender', ['user', 'bot']);
      $table->text('content');
      $table->integer('order');
      $table->boolean('is_flagged')->default(false);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('aliexpress_chat_messages');
  }
};
