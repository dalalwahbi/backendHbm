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
        Schema::table('users', function (Blueprint $table) {
            // Check if the column exists before renaming
            if (Schema::hasColumn('users', 'adress')) {
                $table->renameColumn('adress', 'address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restore the column name if needed
            if (Schema::hasColumn('users', 'address')) {
                $table->renameColumn('address', 'adress');
            }
        });
    }
};
