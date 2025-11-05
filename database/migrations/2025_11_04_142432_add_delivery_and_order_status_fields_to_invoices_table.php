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
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('delivery_status', ['pending', 'delivered'])->default('pending')->after('delivery_request_status');
            $table->enum('order_status', ['pending', 'completed'])->default('pending')->after('delivery_status');
            $table->enum('dispute_status', ['open', 'resolved', 'closed'])->nullable()->after('order_status');
            $table->text('dispute_note')->nullable()->after('dispute_status');
            $table->timestamp('dispute_opened_at')->nullable()->after('dispute_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['delivery_status', 'order_status', 'dispute_status', 'dispute_note', 'dispute_opened_at']);
        });
    }
};
