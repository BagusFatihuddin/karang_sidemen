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
        Schema::create('exports', function (
            Blueprint $table
        ): void {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('exporter');

            $table->unsignedBigInteger('processed_rows')
                ->default(0);

            $table->unsignedBigInteger('total_rows')
                ->default(0);

            $table->string('successful_rows')
                ->nullable();

            $table->string('file_disk');

            $table->string('file_name')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exports');
    }
};