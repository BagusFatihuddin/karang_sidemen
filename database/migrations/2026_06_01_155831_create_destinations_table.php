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
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->text('description');
            $table->text('facilities')->nullable();

            $table->decimal('entry_fee', 10, 2)->nullable();
            $table->decimal('parking_fee', 10, 2)->nullable();
            $table->decimal('rental_price', 10, 2)->nullable();

            $table->enum('destination_type', [
                'camping',
                'air',
                'edukasi',
                'alam',
                'kuliner',
                'lainnya',
            ]);

            $table->string('whatsapp_number', 20)->nullable();
            $table->string('maps_url', 500)->nullable();
            $table->string('cloudinary_folder', 100)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};