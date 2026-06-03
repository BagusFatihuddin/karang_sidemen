<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('promos', function (Blueprint $table) {
        $table->id();

        $table->string('title', 150);

        $table->text('description')
            ->nullable();

        $table->string('image_url', 500)
            ->nullable();

        $table->string('image_public_id', 255)
            ->nullable();

        $table->string('external_url', 500)
            ->nullable();

        $table->boolean('is_active')
            ->default(true);

        $table->date('start_date')
            ->nullable();

        $table->date('end_date')
            ->nullable();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('promos');
}
};
