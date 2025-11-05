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
        Schema::table('vendors', function (Blueprint $table) {
            // Add new combined field
            $table->string('payment_number')->nullable()->after('account_details');
        });

        // Migrate existing data: combine gpay and phonepe if both exist, otherwise use whichever exists
        DB::statement("
            UPDATE vendors 
            SET payment_number = CASE 
                WHEN gpay_number IS NOT NULL AND phonepe_number IS NOT NULL 
                    THEN CONCAT(gpay_number, ' / ', phonepe_number)
                WHEN gpay_number IS NOT NULL 
                    THEN gpay_number
                WHEN phonepe_number IS NOT NULL 
                    THEN phonepe_number
                ELSE NULL
            END
        ");

        Schema::table('vendors', function (Blueprint $table) {
            // Drop old fields
            $table->dropColumn(['gpay_number', 'phonepe_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Add back old fields
            $table->string('gpay_number')->nullable()->after('account_details');
            $table->string('phonepe_number')->nullable()->after('gpay_number');
        });

        // Try to split payment_number back (this is a best-effort approach)
        // If payment_number contains '/', split it, otherwise put in gpay_number
        DB::statement("
            UPDATE vendors 
            SET gpay_number = CASE 
                WHEN payment_number LIKE '%/%' 
                    THEN TRIM(SUBSTRING_INDEX(payment_number, '/', 1))
                ELSE payment_number
            END,
            phonepe_number = CASE 
                WHEN payment_number LIKE '%/%' 
                    THEN TRIM(SUBSTRING_INDEX(payment_number, '/', -1))
                ELSE NULL
            END
            WHERE payment_number IS NOT NULL
        ");

        Schema::table('vendors', function (Blueprint $table) {
            // Drop the combined field
            $table->dropColumn('payment_number');
        });
    }
};
