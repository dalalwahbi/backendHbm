<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStatusColumnInOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the existing status column
            $table->dropColumn('Status'); // Change 'Status' to lowercase 'status' if needed

            // Add the new status column as an enum type
            $table->enum('status', ["en attente", "attribué", "en transit", "livré", "annulé"])
                  ->default('en attente');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the new status column
            $table->dropColumn('status');

            // Add the old status column back (if necessary)
            $table->string('Status')->default('en attente'); // Adjust as necessary
        });
    }
}

