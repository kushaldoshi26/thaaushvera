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
        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            $table->text('prompt');
            $table->longText('response')->nullable();
            $table->string('mode')->nullable();
            $table->integer('input_tokens')->nullable();
            $table->integer('output_tokens')->nullable();
            $table->decimal('estimated_cost', 10, 6)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
    }
};
