<?php

namespace App\Filament\Admin\Resources\Reviews\Tables;

use App\Models\Destination;
use App\Models\Review;
use App\Support\CacheVersion;
use App\Support\UserRole;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reviewer_name')
                    ->label('Reviewer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('destination.name')
                    ->label('Destinasi')
                    ->sortable(),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (int $state): string => str_repeat('*', $state))
                    ->sortable(),

                TextColumn::make('review_text')
                    ->label('Review')
                    ->limit(80)
                    ->searchable(),

                IconColumn::make('photo_url')
                    ->label('Foto')
                    ->boolean()
                    ->state(fn (Review $record): bool => filled($record->photo_url)),

                IconColumn::make('is_pinned_destination')
                    ->label('Pin Dest.')
                    ->boolean(),

                IconColumn::make('is_pinned_global')
                    ->label('Pin Global')
                    ->boolean(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            default => $state ?? '-',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default => 'gray',
                        }
                    )
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('destination_id')
                    ->label('Destinasi')
                    ->options(
                        fn (): array => Destination::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all()
                    )
                    ->searchable(),

                SelectFilter::make('rating')
                    ->label('Rating')
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                    ]),

                SelectFilter::make('status')
                    ->label('Status Review')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                Filter::make('is_pinned_global')
                    ->label('Pinned Global')
                    ->query(
                        fn (Builder $query): Builder =>
                            $query->where('is_pinned_global', true)
                    ),

                Filter::make('is_pinned_destination')
                    ->label('Pinned Destinasi')
                    ->query(
                        fn (Builder $query): Builder =>
                            $query->where('is_pinned_destination', true)
                    ),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('approve')
                    ->label('Approve Saja')
                    ->color('success')
                    ->visible(fn (Review $record): bool => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->action(function (Review $record): void {
                        static::approve($record);

                        Notification::make()
                            ->title('Review approved.')
                            ->success()
                            ->send();
                    }),

                Action::make('approveAndPinDestination')
                    ->label('Approve + Pin Destinasi')
                    ->icon(Heroicon::OutlinedBookmark)
                    ->color('warning')
                    ->visible(
                        fn (Review $record): bool =>
                            $record->status !== 'approved'
                            || ! $record->is_pinned_destination
                    )
                    ->requiresConfirmation()
                    ->action(function (Review $record): void {
                        static::approve($record);

                        $record->update([
                            'is_pinned_destination' => true,
                        ]);
                        static::clearReviewCache();

                        Notification::make()
                            ->title('Review approved dan dipin ke destinasi.')
                            ->success()
                            ->send();
                    }),

                Action::make('approveAndPinGlobal')
                    ->label('Approve + Pin Global')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->color('info')
                    ->visible(
                        fn (Review $record): bool =>
                            $record->status !== 'approved'
                            || ! $record->is_pinned_global
                    )
                    ->requiresConfirmation()
                    ->action(function (Review $record): void {
                        static::approve($record);

                        $record->update([
                            'is_pinned_global' => true,
                        ]);
                        static::clearReviewCache();

                        Notification::make()
                            ->title('Review approved dan dipin global.')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->visible(fn (Review $record): bool => $record->status !== 'rejected')
                    ->requiresConfirmation()
                    ->action(function (Review $record): void {
                        static::reject($record);

                        Notification::make()
                            ->title('Review rejected.')
                            ->success()
                            ->send();
                    }),

                Action::make('toggleDestinationPin')
                    ->label(
                        fn (Review $record): string =>
                            $record->is_pinned_destination
                                ? 'Unpin Destinasi'
                                : 'Pin Destinasi'
                    )
                    ->icon(
                        fn (Review $record): Heroicon =>
                            $record->is_pinned_destination
                                ? Heroicon::OutlinedBookmarkSlash
                                : Heroicon::OutlinedBookmark
                    )
                    ->color(
                        fn (Review $record): string =>
                            $record->is_pinned_destination
                                ? 'gray'
                                : 'warning'
                    )
                    ->visible(fn (Review $record): bool => $record->status === 'approved')
                    ->action(function (Review $record): void {
                        $record->update([
                            'is_pinned_destination' => ! $record->is_pinned_destination,
                        ]);
                        static::clearReviewCache();

                        Notification::make()
                            ->title(
                                $record->is_pinned_destination
                                    ? 'Review pinned to destination.'
                                    : 'Review unpinned from destination.'
                            )
                            ->success()
                            ->send();
                    }),

                Action::make('toggleGlobalPin')
                    ->label(
                        fn (Review $record): string =>
                            $record->is_pinned_global
                                ? 'Unpin Global'
                                : 'Pin Global'
                    )
                    ->icon(
                        fn (Review $record): Heroicon =>
                            $record->is_pinned_global
                                ? Heroicon::OutlinedBookmarkSlash
                                : Heroicon::OutlinedGlobeAlt
                    )
                    ->color(
                        fn (Review $record): string =>
                            $record->is_pinned_global
                                ? 'gray'
                                : 'info'
                    )
                    ->visible(fn (Review $record): bool => $record->status === 'approved')
                    ->action(function (Review $record): void {
                        $record->update([
                            'is_pinned_global' => ! $record->is_pinned_global,
                        ]);
                        static::clearReviewCache();

                        Notification::make()
                            ->title(
                                $record->is_pinned_global
                                    ? 'Review pinned globally.'
                                    : 'Review unpinned globally.'
                            )
                            ->success()
                            ->send();
                    }),

                DeleteAction::make()
                    ->visible(
                        fn (): bool =>
                            Auth::user()?->role === UserRole::SUPER_ADMIN
                    ),
            ])
            ->toolbarActions([
                BulkAction::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $records->each(fn (Review $record): Review => static::approve($record));
                    })
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('approveAndPinDestination')
                    ->label('Approve + Pin Destinasi')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $records->each(function (Review $record): void {
                            static::approve($record);

                            $record->update([
                                'is_pinned_destination' => true,
                            ]);
                        });

                        static::clearReviewCache();
                    })
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('approveAndPinGlobal')
                    ->label('Approve + Pin Global')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $records->each(function (Review $record): void {
                            static::approve($record);

                            $record->update([
                                'is_pinned_global' => true,
                            ]);
                        });

                        static::clearReviewCache();
                    })
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $records->each(fn (Review $record): Review => static::reject($record));
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    protected static function approve(Review $review): Review
    {
        $review->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        static::clearReviewCache();

        return $review;
    }

    protected static function reject(Review $review): Review
    {
        $review->update([
            'status' => 'rejected',
            'is_pinned_destination' => false,
            'is_pinned_global' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);
        static::clearReviewCache();

        return $review;
    }

    protected static function clearReviewCache(): void
    {
        CacheVersion::bump('reviews:version');
    }
}
