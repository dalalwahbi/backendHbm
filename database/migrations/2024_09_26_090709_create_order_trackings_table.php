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
        Schema::create('order_tracking', function (Blueprint $table) {
            $table->id('TrackingID');
            $table->unsignedBigInteger('OrderID'); // Define OrderID column
            $table->foreign('OrderID')->references('OrderID')->on('orders')->onDelete('cascade'); // Correct foreign key
            $table->string('CurrentStatus');
            $table->timestamp('StatusDate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_trackings');
    }
};
