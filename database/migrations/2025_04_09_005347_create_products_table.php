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
            $table->string('featured_image');
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['available', 'not available'])->default('available');
            $table->foreignId('posted_by');
            $table->foreignId('category_id');
            $table->timestamps();
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