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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('OrderItemID');
            $table->unsignedBigInteger('OrderID'); // Define OrderID column
            $table->foreign('OrderID')->references('OrderID')->on('orders')->onDelete('cascade'); // Correct foreign key
            $table->unsignedBigInteger('ProductID');
            $table->foreign('ProductID')->references('ProductID')->on('products')->onDelete('cascade');
            $table->integer('Quantity');
            $table->decimal('Price', 8, 2);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
