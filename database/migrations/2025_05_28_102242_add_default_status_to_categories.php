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
         Schema::table('categories', function (Blueprint $table) {
        $table->string('status')->default('Active')->change();
    });
    
    // Update existing null values to 'Active'
    DB::table('categories')->whereNull('status')->update(['status' => 'Active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('categories', function (Blueprint $table) {
        $table->string('status')->nullable()->change();
    });
    }
};
