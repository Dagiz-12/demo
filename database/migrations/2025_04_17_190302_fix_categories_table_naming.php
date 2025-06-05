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
        
        // Check which tables exist
        $catagoriesExists = Schema::hasTable('catagories');
        $categoriesExists = Schema::hasTable('categories');
        
        if ($catagoriesExists && !$categoriesExists) {
            // Safe to rename
            Schema::rename('catagories', 'categories');
        } elseif ($catagoriesExists && $categoriesExists) {
            // More complex migration needed
            $this->mergeTables();
        }


        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
