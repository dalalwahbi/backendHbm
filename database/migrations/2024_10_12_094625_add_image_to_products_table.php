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
        Schema::table('products', function (Blueprint $table) {
            // Add image field, you can make it nullable if products don't always have images
            $table->string('image')->nullable()->after('Price'); // Adjust 'Price' if needed to insert after another column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the image column if rolling back the migration
            $table->dropColumn('image');
        });
    }
};
