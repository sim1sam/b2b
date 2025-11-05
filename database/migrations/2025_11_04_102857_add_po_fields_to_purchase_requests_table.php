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
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->decimal('amount_inr', 15, 2)->nullable()->after('amount')->comment('Original amount in INR');
            $table->boolean('is_gst_payment')->default(false)->after('amount_inr');
            $table->decimal('amount_bdt', 15, 2)->nullable()->after('is_gst_payment')->comment('Calculated amount in BDT');
            $table->string('po_number')->nullable()->unique()->after('request_number')->comment('Purchase Order Number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn(['amount_inr', 'is_gst_payment', 'amount_bdt', 'po_number']);
        });
    }
};
