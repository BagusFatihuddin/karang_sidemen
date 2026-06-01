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
        Schema::create('daily_visits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('destination_id')
                ->constrained()
                ->restrictOnDelete();

            $table->date('date');

            $table->unsignedInteger('visitor_count')
                ->default(0);

            $table->decimal('revenue', 12, 2)
                ->default(0);

            $table->decimal('expense', 12, 2)
                ->default(0);

            $table->foreignId('recorded_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique([
                'destination_id',
                'date',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_visits');
    }
};