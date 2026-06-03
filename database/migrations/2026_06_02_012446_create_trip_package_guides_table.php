<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('trip_package_guides', function (Blueprint $table) {
        $table->id();

        $table->foreignId('trip_package_id')
            ->constrained('trip_packages')
            ->cascadeOnDelete();

        $table->foreignId('guide_id')
            ->constrained('guides')
            ->restrictOnDelete();

        $table->unique([
            'trip_package_id',
            'guide_id',
        ]);
    });
}

public function down(): void
{
    Schema::dropIfExists(
        'trip_package_guides'
    );
}
};
