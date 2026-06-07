<?php

namespace App\Filament\Admin\Resources\Reviews\Infolists;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Review')
                    ->schema([
                        TextEntry::make('reviewer_name')
                            ->label('Reviewer'),

                        TextEntry::make('reviewer_city')
                            ->label('Kota'),

                        TextEntry::make('destination.name')
                            ->label('Destinasi')
                            ->placeholder('-'),

                        TextEntry::make('rating')
                            ->label('Rating')
                            ->formatStateUsing(fn (int $state): string => str_repeat('*', $state)),

                        TextEntry::make('review_text')
                            ->label('Review')
                            ->columnSpanFull(),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(
                                fn (?string $state): string => match ($state) {
                                    'pending' => 'Pending',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                    default => $state ?? '-',
                                }
                            ),

                        TextEntry::make('is_pinned_destination')
                            ->label('Pinned Destinasi')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                            ->color(fn (bool $state): string => $state ? 'warning' : 'gray'),

                        TextEntry::make('is_pinned_global')
                            ->label('Pinned Global')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                            ->color(fn (bool $state): string => $state ? 'info' : 'gray'),

                        ImageEntry::make('photo_url')
                            ->label('Foto')
                            ->visible(fn ($record): bool => filled($record->photo_url))
                            ->imageHeight(240),

                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y H:i'),

                        TextEntry::make('approvedBy.name')
                            ->label('Approved By')
                            ->placeholder('-'),

                        TextEntry::make('approved_at')
                            ->label('Approved At')
                            ->dateTime('d M Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}
