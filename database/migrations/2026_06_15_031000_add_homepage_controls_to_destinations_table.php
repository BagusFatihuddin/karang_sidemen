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
            $table->boolean('is_featured_homepage')
                ->default(false)
                ->after('source_urls');
            $table->unsignedInteger('homepage_sort_order')
                ->nullable()
                ->after('is_featured_homepage');
            $table->string('homepage_label')
                ->nullable()
                ->after('homepage_sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn([
                'is_featured_homepage',
                'homepage_sort_order',
                'homepage_label',
            ]);
        });
    }
};
