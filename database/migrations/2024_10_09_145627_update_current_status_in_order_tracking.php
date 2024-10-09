<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCurrentStatusInOrderTracking extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_tracking', function (Blueprint $table) {
            // Modify CurrentStatus from string to enum
            $table->enum('CurrentStatus', ["en attente", "attribué", "en transit", "livré", "annulé"])
                  ->default('en attente')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_tracking', function (Blueprint $table) {
            // Revert CurrentStatus back to string
            $table->string('CurrentStatus')->change();
        });
    }
}
