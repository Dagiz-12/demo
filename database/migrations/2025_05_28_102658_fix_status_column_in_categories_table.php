<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Make the column NOT NULL with default 'Active'
        Schema::table('categories', function (Blueprint $table) {
            $table->string('status')->default('Active')->nullable(false)->change();
        });
        
        // Update any existing NULL values to 'Active'
        DB::table('categories')->whereNull('status')->update(['status' => 'Active']);
    }

    public function down()
    {
        // Revert back to nullable if needed
        Schema::table('categories', function (Blueprint $table) {
            $table->string('status')->nullable()->default(null)->change();
        });
    }
};