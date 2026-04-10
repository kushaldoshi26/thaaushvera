<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'ai_used')) {
                $table->integer('ai_used')->default(0)->after('address');
            }
            if (!Schema::hasColumn('users', 'ai_limit')) {
                $table->integer('ai_limit')->default(1000)->after('ai_used');
            }
            if (!Schema::hasColumn('users', 'monthly_tokens')) {
                $table->integer('monthly_tokens')->default(0)->after('ai_limit');
            }
            if (!Schema::hasColumn('users', 'monthly_cost')) {
                $table->decimal('monthly_cost', 10, 6)->default(0)->after('monthly_tokens');
            }
        });

        // Also add image_path to ai_logs if not exists
        if (Schema::hasTable('ai_logs') && !Schema::hasColumn('ai_logs', 'image_path')) {
            Schema::table('ai_logs', function (Blueprint $table) {
                $table->string('image_path')->nullable()->after('estimated_cost');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ai_used', 'ai_limit', 'monthly_tokens', 'monthly_cost']);
        });
    }
};
