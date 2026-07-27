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
            $table->string('name');
            $table->string('shape');
            $table->string('color');
            $table->decimal('price', 12, 0);
            $table->string('category')->default('Sunglasses');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('best_seller')->default(false);
            $table->decimal('rating', 3, 1)->default(4.5);
            $table->integer('reviews')->default(0);
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
