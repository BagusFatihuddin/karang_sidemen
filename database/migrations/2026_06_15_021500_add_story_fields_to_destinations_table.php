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
        Schema::table('destinations', function (Blueprint $table) {
            $table->string('slug', 170)->nullable()->unique()->after('name');
            $table->text('short_description')->nullable()->after('description');
            $table->string('tourism_vibe', 160)->nullable()->after('facilities');
            $table->json('tags')->nullable()->after('tourism_vibe');
            $table->json('highlights')->nullable()->after('tags');
            $table->json('activity_keywords')->nullable()->after('highlights');
            $table->json('source_urls')->nullable()->after('activity_keywords');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'slug',
                'short_description',
                'tourism_vibe',
                'tags',
                'highlights',
                'activity_keywords',
                'source_urls',
            ]);
        });
    }
};
