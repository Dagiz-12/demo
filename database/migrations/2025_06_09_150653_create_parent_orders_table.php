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
                Schema::create('parent_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos1_id')->constrained('pos');
            $table->foreignId('pos2_id')->constrained('pos');
            $table->text('memo')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_orders');
    }
};
