<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add admin-specific columns (check if they don't exist)
            if (!Schema::hasColumn('users', 'admin_id')) {
                $table->string('admin_id')->nullable()->unique()->after('role');
            }
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('admin_id');
            }
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->text('two_factor_secret')->nullable()->after('two_factor_enabled');
            }
            if (!Schema::hasColumn('users', 'two_factor_verified_at')) {
                $table->timestamp('two_factor_verified_at')->nullable()->after('two_factor_secret');
            }
            if (!Schema::hasColumn('users', 'requires_password_change')) {
                $table->boolean('requires_password_change')->default(false)->after('two_factor_verified_at');
            }
            
            // OAuth fields
            if (!Schema::hasColumn('users', 'oauth_provider')) {
                $table->string('oauth_provider')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'oauth_id')) {
                $table->string('oauth_id')->nullable()->after('oauth_provider');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'admin_id',
                'two_factor_enabled',
                'two_factor_secret',
                'two_factor_verified_at',
                'requires_password_change',
                'last_login_at',
                'oauth_provider',
                'oauth_id'
            ]);
        });
    }
};
