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
        Schema::table('orders', function (Blueprint $table) {
            // Add the PaymentID column
            $table->unsignedBigInteger('PaymentID')->after('SupplierID')->nullable(); // You can make it nullable if needed

            // Add the foreign key constraint
            $table->foreign('PaymentID')->references('PaymentID')->on('payments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the foreign key and the column
            $table->dropForeign(['PaymentID']);
            $table->dropColumn('PaymentID');
        });
    }
};
