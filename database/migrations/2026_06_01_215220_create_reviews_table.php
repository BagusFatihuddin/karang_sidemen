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

            $table->string('visitor_name', 100);

            $table->unsignedTinyInteger('rating');

            $table->text('review');

            $table->boolean('is_approved')
                ->default(false);

            $table->boolean('is_pinned')
                ->default(false);

            $table->boolean('is_pinned_homepage')
                ->default(false);

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