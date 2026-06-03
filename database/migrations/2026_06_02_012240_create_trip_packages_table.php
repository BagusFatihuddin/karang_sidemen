<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('trip_packages', function (Blueprint $table) {
        $table->id();

        $table->string('name', 150);

        $table->text('description')
            ->nullable();

        $table->decimal('price', 12, 2)
            ->nullable();

        $table->string('image_url', 500)
            ->nullable();

        $table->string('image_public_id', 255)
            ->nullable();

        $table->boolean('is_active')
            ->default(true);

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('trip_packages');
}
};
