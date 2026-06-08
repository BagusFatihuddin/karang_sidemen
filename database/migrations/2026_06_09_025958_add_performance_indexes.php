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
        Schema::table('visitors', function (Blueprint $table) {
            // For sorting/filtering recent visitors
            $table->index('created_at');
        });

        Schema::table('bookings', function (Blueprint $table) {
            // For sorting/filtering recent bookings and status queries
            $table->index('created_at');
            $table->index('status');
        });

        Schema::table('daily_visits', function (Blueprint $table) {
            // For date range queries in reports
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status']);
        });

        Schema::table('daily_visits', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });
    }
};
