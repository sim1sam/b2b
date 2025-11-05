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
            $table->string('apps_home_url')->nullable()->after('app_name');
            $table->text('footer_copyright_text')->nullable()->after('apps_home_url');
            $table->string('footer_developer_name')->nullable()->after('footer_copyright_text');
            $table->string('footer_developer_link')->nullable()->after('footer_developer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['apps_home_url', 'footer_copyright_text', 'footer_developer_name', 'footer_developer_link']);
        });
    }
};
