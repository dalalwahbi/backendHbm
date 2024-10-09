<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePaymentStatusColumnInPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Change the existing 'PaymentStatus' column to an enum type
            $table->enum('PaymentStatus', ["en attente", "payé", "annulé"])
                  ->default('en attente')
                  ->change(); // Use 'change()' to modify the column type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Revert the 'PaymentStatus' column back to a string type
            $table->string('PaymentStatus')->change();
        });
    }
}

