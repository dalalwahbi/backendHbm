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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('OrderID');
            $table->foreignId(column: 'SupplierID')->constrained('users')->onDelete('cascade'); // Foreign key for SupplierID
            $table->unsignedBigInteger('CustomerID'); // Use unsignedBigInteger for consistency with CustomerID reference
            $table->string('Status'); // Order Status
            $table->timestamps();
        
            // Foreign key constraint for CustomerID
            $table->foreign('CustomerID')->references('CustomerID')->on('customer_infos')->onDelete('cascade');
        });
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
