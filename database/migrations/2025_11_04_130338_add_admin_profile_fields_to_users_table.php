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
        Schema::table('users', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('lowest_shipping_charge_per_kg');
            $table->string('admin_logo')->nullable()->after('company_name');
            $table->string('address_line1')->nullable()->after('admin_logo');
            $table->string('address_line2')->nullable()->after('address_line1');
            $table->string('district')->nullable()->after('address_line2');
            $table->string('country')->nullable()->after('district');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'admin_logo', 'address_line1', 'address_line2', 'district', 'country']);
        });
    }
};
