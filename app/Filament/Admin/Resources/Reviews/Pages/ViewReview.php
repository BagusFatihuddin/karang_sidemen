<?php

namespace App\Filament\Admin\Resources\Reviews\Pages;

use App\Filament\Admin\Resources\Reviews\ReviewResource;
use App\Models\Review;
use App\Support\CacheVersion;
use App\Support\UserRole;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ViewReview extends ViewRecord
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve Saja')
                ->color('success')
                ->visible(fn (): bool => $this->record->status !== 'approved')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->approve($this->record);

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
                    fn (): bool =>
                        $this->record->status !== 'approved'
                        || ! $this->record->is_pinned_destination
                )
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->approve($this->record);

                    $this->record->update([
                        'is_pinned_destination' => true,
                    ]);

                    $this->clearReviewCache();
                    $this->record->refresh();

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
                    fn (): bool =>
                        $this->record->status !== 'approved'
                        || ! $this->record->is_pinned_global
                )
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->approve($this->record);

                    $this->record->update([
                        'is_pinned_global' => true,
                    ]);

                    $this->clearReviewCache();
                    $this->record->refresh();

                    Notification::make()
                        ->title('Review approved dan dipin global.')
                        ->success()
                        ->send();
                }),

            Action::make('toggleDestinationPin')
                ->label(
                    fn (): string =>
                        $this->record->is_pinned_destination
                            ? 'Unpin Destinasi'
                            : 'Pin Destinasi'
                )
                ->icon(
                    fn (): Heroicon =>
                        $this->record->is_pinned_destination
                            ? Heroicon::OutlinedBookmarkSlash
                            : Heroicon::OutlinedBookmark
                )
                ->color(
                    fn (): string =>
                        $this->record->is_pinned_destination
                            ? 'gray'
                            : 'warning'
                )
                ->visible(fn (): bool => $this->record->status === 'approved')
                ->action(function (): void {
                    $this->record->update([
                        'is_pinned_destination' => ! $this->record->is_pinned_destination,
                    ]);

                    $this->clearReviewCache();
                    $this->record->refresh();

                    Notification::make()
                        ->title(
                            $this->record->is_pinned_destination
                                ? 'Review dipin ke destinasi.'
                                : 'Pin destinasi dilepas.'
                        )
                        ->success()
                        ->send();
                }),

            Action::make('toggleGlobalPin')
                ->label(
                    fn (): string =>
                        $this->record->is_pinned_global
                            ? 'Unpin Global'
                            : 'Pin Global'
                )
                ->icon(
                    fn (): Heroicon =>
                        $this->record->is_pinned_global
                            ? Heroicon::OutlinedBookmarkSlash
                            : Heroicon::OutlinedGlobeAlt
                )
                ->color(
                    fn (): string =>
                        $this->record->is_pinned_global
                            ? 'gray'
                            : 'info'
                )
                ->visible(fn (): bool => $this->record->status === 'approved')
                ->action(function (): void {
                    $this->record->update([
                        'is_pinned_global' => ! $this->record->is_pinned_global,
                    ]);

                    $this->clearReviewCache();
                    $this->record->refresh();

                    Notification::make()
                        ->title(
                            $this->record->is_pinned_global
                                ? 'Review dipin global.'
                                : 'Pin global dilepas.'
                        )
                        ->success()
                        ->send();
                }),

            Action::make('reject')
                ->label('Reject')
                ->color('danger')
                ->visible(fn (): bool => $this->record->status !== 'rejected')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->reject($this->record);

                    Notification::make()
                        ->title('Review rejected.')
                        ->success()
                        ->send();
                }),

            DeleteAction::make()
                ->visible(
                    fn (): bool =>
                        Auth::user()?->role === UserRole::SUPER_ADMIN
                ),
        ];
    }

    protected function approve(Review $review): void
    {
        $review->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $this->clearReviewCache();
        $this->record->refresh();
    }

    protected function reject(Review $review): void
    {
        $review->update([
            'status' => 'rejected',
            'is_pinned_destination' => false,
            'is_pinned_global' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        $this->clearReviewCache();
        $this->record->refresh();
    }

    protected function clearReviewCache(): void
    {
        CacheVersion::bump('reviews:version');
    }
}
