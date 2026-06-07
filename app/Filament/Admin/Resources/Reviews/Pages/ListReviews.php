<?php

namespace App\Filament\Admin\Resources\Reviews\Pages;

use App\Filament\Admin\Resources\Reviews\ReviewResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make('Pending')
                ->query(fn (Builder $query): Builder => $query->where('status', 'pending')),

            'approved' => Tab::make('Approved')
                ->query(fn (Builder $query): Builder => $query->where('status', 'approved')),

            'rejected' => Tab::make('Rejected')
                ->query(fn (Builder $query): Builder => $query->where('status', 'rejected')),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'pending';
    }
}
