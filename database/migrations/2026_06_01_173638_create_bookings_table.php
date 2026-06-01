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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('booking_code', 20)
                ->unique();

            $table->foreignId('visitor_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('guest_name', 100)
                ->nullable();

            $table->string('guest_phone', 20)
                ->nullable();

            $table->string('guest_city', 100)
                ->nullable();

            $table->foreignId('destination_id')
                ->constrained()
                ->restrictOnDelete();

            $table->date('booking_date');

            $table->unsignedInteger('total_person')
                ->default(1);

            $table->text('notes')
                ->nullable();

            $table->enum('status', [
                'pending',
                'confirmed',
                'cancelled',
                'completed',
            ])->default('pending');

            $table->foreignId('recorded_by')
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
        Schema::dropIfExists('bookings');
    }
};