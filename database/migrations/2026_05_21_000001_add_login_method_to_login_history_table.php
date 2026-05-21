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
        Schema::table('login_history', function (Blueprint $table) {
            $table->string('login_method', 20)->default('email')->after('ip_address');
            $table->string('user_agent')->nullable()->after('login_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_history', function (Blueprint $table) {
            $table->dropColumn(['login_method', 'user_agent']);
        });
    }
};
