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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('whatsapp_number', 20);

            $table->enum('origin_category', [
                'lombok_tengah',
                'lombok_lainnya',
                'luar_lombok',
                'mancanegara',
            ]);

            $table->string('origin_city', 100);

            $table->enum('visit_type', [
                'sendiri',
                'pasangan',
                'keluarga',
                'rombongan',
            ]);

            $table->unsignedInteger('group_size')
                ->default(1);

            $table->enum('referral_source', [
                'instagram',
                'whatsapp',
                'teman',
                'google',
                'lainnya',
            ]);

            $table->string('referral_other', 100)
                ->nullable();

            $table->foreignId('destination_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('recorded_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('visited_at')
                ->nullable();

            $table->timestamps();

            $table->index('whatsapp_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};