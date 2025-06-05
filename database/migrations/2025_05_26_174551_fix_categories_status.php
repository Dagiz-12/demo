<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Update all null statuses to 'Active'
        DB::table('categories')->whereNull('status')->update(['status' => 'Active']);
    
    // Change column to be not nullable with default
    Schema::table('categories', function (Blueprint $table) {
        $table->string('status')->default('Active')->nullable(false)->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
       Schema::table('categories', function (Blueprint $table) {
        $table->string('status')->nullable()->change();
    });
    }
};
