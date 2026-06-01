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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('review_token_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('visitor_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('destination_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('reviewer_name', 100);

            $table->string('reviewer_city', 100);

            $table->unsignedTinyInteger('rating');

            $table->text('review_text')
                ->nullable();

            $table->string('photo_url', 500)
                ->nullable();

            $table->string('photo_public_id', 255)
                ->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ]);

            $table->boolean('is_pinned_destination')
                ->default(false);

            $table->boolean('is_pinned_global')
                ->default(false);

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};