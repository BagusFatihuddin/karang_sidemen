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

            $table->foreignId('visitor_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('destination_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('token', 255)
                ->unique();

            $table->timestamp('expires_at');

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();
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