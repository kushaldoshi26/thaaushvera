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
        // 1. Rename existing login_history to login_history_old
        Schema::rename('login_history', 'login_history_old');

        // 2. Create a new login_history table with user_id nullable and set null on delete
        Schema::create('login_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('login_method', 20)->default('email');
            $table->string('user_agent')->nullable();
            $table->timestamp('login_time');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        // 3. Copy existing records from login_history_old to login_history
        if (Schema::hasTable('login_history_old')) {
            $records = DB::table('login_history_old')->get();
            foreach ($records as $record) {
                // Check if user still exists to preserve foreign key validity in strict systems,
                // although since we are doing set null, if they don't exist, we can just insert null.
                $userExists = false;
                if ($record->user_id !== null) {
                    $userExists = DB::table('users')->where('id', $record->user_id)->exists();
                }
                
                DB::table('login_history')->insert([
                    'id' => $record->id,
                    'user_id' => $userExists ? $record->user_id : null,
                    'ip_address' => $record->ip_address ?? null,
                    'login_method' => $record->login_method ?? 'email',
                    'user_agent' => $record->user_agent ?? null,
                    'login_time' => $record->login_time,
                    'created_at' => $record->created_at ?? now(),
                    'updated_at' => $record->updated_at ?? now(),
                ]);
            }

            // 4. Drop the old table
            Schema::dropIfExists('login_history_old');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('login_history', 'login_history_old');

        Schema::create('login_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ip_address')->nullable();
            $table->string('login_method', 20)->default('email');
            $table->string('user_agent')->nullable();
            $table->timestamp('login_time');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        if (Schema::hasTable('login_history_old')) {
            $records = DB::table('login_history_old')->get();
            foreach ($records as $record) {
                if ($record->user_id !== null) {
                    DB::table('login_history')->insert([
                        'id' => $record->id,
                        'user_id' => $record->user_id,
                        'ip_address' => $record->ip_address,
                        'login_method' => $record->login_method,
                        'user_agent' => $record->user_agent,
                        'login_time' => $record->login_time,
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at,
                    ]);
                }
            }
            Schema::dropIfExists('login_history_old');
        }
    }
};
