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
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('admin')->after('password');
            }
            if (!Schema::hasColumn('users', 'ai_limit')) {
                $table->integer('ai_limit')->default(50)->after('role');
            }
            if (!Schema::hasColumn('users', 'ai_used')) {
                $table->integer('ai_used')->default(0)->after('ai_limit');
            }
            if (!Schema::hasColumn('users', 'monthly_tokens')) {
                $table->integer('monthly_tokens')->default(0)->after('ai_used');
            }
            if (!Schema::hasColumn('users', 'monthly_cost')) {
                $table->decimal('monthly_cost', 10, 6)->default(0)->after('monthly_tokens');
            }
            if (!Schema::hasColumn('users', 'plan_id')) {
                $table->foreignId('plan_id')->nullable()->constrained('ai_plans')->after('monthly_cost');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role','ai_limit','ai_used','monthly_tokens','monthly_cost']);
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');
        });
    }
};
