<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->index('visitors', ['visited_at'], 'visitors_visited_at_index');
        $this->index('visitors', ['origin_category'], 'visitors_origin_category_index');
        $this->index('visitors', ['visit_type'], 'visitors_visit_type_index');

        $this->index('reviews', ['status', 'created_at'], 'reviews_status_created_at_index');
        $this->index('reviews', ['status', 'is_pinned_global'], 'reviews_status_pinned_global_index');
        $this->index('reviews', ['destination_id', 'status', 'is_pinned_destination'], 'reviews_destination_status_pinned_index');

        $this->index('destinations', ['is_active'], 'destinations_is_active_index');
        $this->index('destinations', ['destination_type'], 'destinations_destination_type_index');
        $this->index('destinations', ['is_featured_homepage', 'homepage_sort_order'], 'destinations_featured_homepage_sort_index');

        $this->index('trip_packages', ['is_active', 'created_at'], 'trip_packages_active_created_at_index');
        $this->index('guides', ['is_active', 'created_at'], 'guides_active_created_at_index');

        $this->index('promos', ['is_active', 'start_date', 'end_date'], 'promos_active_dates_index');
    }

    public function down(): void
    {
        $this->dropIndex('promos', 'promos_active_dates_index');

        $this->dropIndex('guides', 'guides_active_created_at_index');
        $this->dropIndex('trip_packages', 'trip_packages_active_created_at_index');

        $this->dropIndex('destinations', 'destinations_featured_homepage_sort_index');
        $this->dropIndex('destinations', 'destinations_destination_type_index');
        $this->dropIndex('destinations', 'destinations_is_active_index');

        $this->dropIndex('reviews', 'reviews_destination_status_pinned_index');
        $this->dropIndex('reviews', 'reviews_status_pinned_global_index');
        $this->dropIndex('reviews', 'reviews_status_created_at_index');

        $this->dropIndex('visitors', 'visitors_visit_type_index');
        $this->dropIndex('visitors', 'visitors_origin_category_index');
        $this->dropIndex('visitors', 'visitors_visited_at_index');
    }

    /**
     * @param  list<string>  $columns
     */
    private function index(string $table, array $columns, string $name): void
    {
        if ($this->hasIndex($table, $name)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($columns, $name): void {
            $table->index($columns, $name);
        });
    }

    private function dropIndex(string $table, string $name): void
    {
        if (! $this->hasIndex($table, $name)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($name): void {
            $table->dropIndex($name);
        });
    }

    private function hasIndex(string $table, string $name): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $name)
            ->exists();
    }
};
