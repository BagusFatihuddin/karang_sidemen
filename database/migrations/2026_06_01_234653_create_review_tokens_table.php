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
        Schema::create('review_tokens', function (Blueprint $table) {
            $table->id();

            $table->string('token', 100)
                ->unique();

            $table->foreignId('visitor_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('destination_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('generated_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->boolean('is_used')
                ->default(false);

            $table->timestamp('expires_at');

            $table->timestamp('used_at')
                ->nullable();

            $table->timestamp('created_at')
                ->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_tokens');
    }
};