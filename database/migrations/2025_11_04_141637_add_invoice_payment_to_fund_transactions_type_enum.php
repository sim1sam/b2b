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
    public function up(): void
    {
        // Modify enum to include 'invoice_payment'
        DB::statement("ALTER TABLE `fund_transactions` MODIFY COLUMN `type` ENUM('deposit', 'withdrawal', 'purchase', 'invoice_payment') DEFAULT 'deposit'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum (remove invoice_payment)
        // Note: This will fail if there are any rows with 'invoice_payment' type
        DB::statement("ALTER TABLE `fund_transactions` MODIFY COLUMN `type` ENUM('deposit', 'withdrawal', 'purchase') DEFAULT 'deposit'");
    }
};
