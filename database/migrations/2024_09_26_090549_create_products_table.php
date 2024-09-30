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
            $table->id('ProductID'); // Primary Key
            $table->string('Name'); // Product Name
            $table->text('Description')->nullable(); // Use 'text' for longer descriptions, and nullable in case it's optional
            $table->decimal('Price', 8, 2); // Price of the product
            $table->integer('StockQuantity'); // Stock quantity of the product
            $table->string('Category'); // Changed 'categorie' to 'Category' for consistency in naming
            $table->timestamps(); // Automatically managed 'created_at' and 'updated_at'
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
