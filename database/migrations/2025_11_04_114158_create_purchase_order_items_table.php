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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('shipping_charge_id')->nullable()->constrained()->onDelete('set null');
            $table->string('item_name');
            $table->decimal('quantity', 10, 2);
            $table->decimal('weight', 10, 2)->default(0); // Weight in kg
            $table->decimal('rate_per_unit', 15, 2);
            $table->decimal('item_cost', 15, 2); // quantity * rate_per_unit
            $table->decimal('shipping_cost', 15, 2)->default(0); // weight * shipping_charge_per_kg
            $table->decimal('total_cost', 15, 2); // item_cost + shipping_cost
            $table->timestamps();
            
            $table->index('purchase_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
