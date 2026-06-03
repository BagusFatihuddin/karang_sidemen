<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('guides', function (Blueprint $table) {
        $table->id();

        $table->string('name', 100);

        $table->text('bio')
            ->nullable();

        $table->string('experience', 255)
            ->nullable();

        $table->string('photo_url', 500)
            ->nullable();

        $table->string('photo_public_id', 255)
            ->nullable();

        $table->boolean('is_active')
            ->default(true);

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('guides');
}
};
