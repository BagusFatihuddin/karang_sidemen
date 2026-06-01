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

        $table->string('booking_code', 10)
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

        $table->date('checkin_date');

        $table->date('checkout_date')
            ->nullable();

        $table->decimal('total_price', 12, 2)
            ->nullable();

        $table->enum('status', [
            'pending',
            'confirmed',
            'cancelled',
            'completed',
        ]);

        $table->foreignId('created_by')
            ->constrained('users')
            ->restrictOnDelete();

        $table->timestamp('arrived_at')
            ->nullable();

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