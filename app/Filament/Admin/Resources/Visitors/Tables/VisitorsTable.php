<?php

namespace App\Filament\Admin\Resources\Visitors\Tables;

use App\Filament\Admin\Resources\Visitors\Exporters\VisitorExporter;
use App\Models\ReviewToken;
use App\Models\Visitor;
use App\Support\AppSettings;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VisitorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('visited_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                ViewColumn::make('actions')
                    ->label('')
                    ->view('filament.admin.columns.visitor-actions'),

                TextColumn::make('origin_category')
                    ->label('Kategori Asal')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'lombok_tengah' => 'Lombok Tengah',
                            'lombok_lainnya' => 'Lombok Lainnya',
                            'luar_lombok' => 'Luar Lombok',
                            'mancanegara' => 'Mancanegara',
                            default => $state ?? '-',
                        }
                    )
                    ->sortable(),

                TextColumn::make('origin_city')
                    ->label('Kota Asal')
                    ->sortable(),

                TextColumn::make('visit_type')
                    ->label('Tipe Kunjungan')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'sendiri' => 'Sendiri',
                            'pasangan' => 'Pasangan',
                            'keluarga' => 'Keluarga',
                            'rombongan' => 'Rombongan',
                            default => $state ?? '-',
                        }
                    )
                    ->sortable(),

                TextColumn::make('destination.name')
                    ->label('Destinasi')
                    ->sortable(),

                TextColumn::make('recordedBy.name')
                    ->label('Dicatat Oleh')
                    ->sortable(),

                TextColumn::make('visited_at')
                    ->label('Tanggal Kunjungan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('origin_category')
                    ->label('Kategori Asal')
                    ->options([
                        'lombok_tengah' => 'Lombok Tengah',
                        'lombok_lainnya' => 'Lombok Lainnya',
                        'luar_lombok' => 'Luar Lombok',
                        'mancanegara' => 'Mancanegara',
                    ]),

                SelectFilter::make('visit_type')
                    ->label('Tipe Kunjungan')
                    ->options([
                        'sendiri' => 'Sendiri',
                        'pasangan' => 'Pasangan',
                        'keluarga' => 'Keluarga',
                        'rombongan' => 'Rombongan',
                    ]),

                SelectFilter::make('destination_id')
                    ->label('Destinasi')
                    ->relationship(
                        name: 'destination',
                        titleAttribute: 'name'
                    )
                    ->searchable()
                    ->preload(),

                Filter::make('visited_at')
                    ->label('Tanggal Kunjungan')
                    ->schema([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),

                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (
                        Builder $query,
                        array $data
                    ): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (
                                    Builder $query,
                                    $date
                                ): Builder => $query->whereDate(
                                    'visited_at',
                                    '>=',
                                    $date
                                )
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (
                                    Builder $query,
                                    $date
                                ): Builder => $query->whereDate(
                                    'visited_at',
                                    '<=',
                                    $date
                                )
                            );
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Export')
                    ->exporter(VisitorExporter::class),
            ])
            ->recordActions([
                Action::make('sendReviewLink')
                    ->label('💬 Kirim Link Review')
                    ->icon('heroicon-m-chat-bubble-bottom-center-text')
                    ->color('success')
                    ->tooltip('Kirim link review ke WhatsApp wisatawan')
                    ->action(
                        fn (Visitor $record) =>
                        static::openReviewWhatsApp($record)
                    ),

                ViewAction::make(),
            ]);
    }

    protected static function openReviewWhatsApp(
        Visitor $visitor
    ) {
        $phone = static::normalizePhone(
            $visitor->whatsapp_number
        );

        if ($phone === null) {
            Notification::make()
                ->title('Nomor WhatsApp tidak valid.')
                ->danger()
                ->send();

            return null;
        }

        $reviewToken = static::getOrCreateReviewToken(
            $visitor
        );

        $reviewUrl = static::buildFrontendReviewUrl(
            $reviewToken->token
        );

        $message = sprintf(
            'Halo %s, terima kasih telah berkunjung. Silakan beri review di sini: %s',
            $visitor->name,
            $reviewUrl
        );

        return redirect()->away(
            'https://wa.me/' . $phone .
            '?text=' . urlencode($message)
        );
    }

    protected static function getOrCreateReviewToken(
        Visitor $visitor
    ): ReviewToken {
        $usableToken = $visitor
            ->reviewTokens()
            ->where('destination_id', $visitor->destination_id)
            ->latest('created_at')
            ->get()
            ->first(
                fn (ReviewToken $token): bool =>
                $token->isUsable()
            );

        if ($usableToken) {
            return $usableToken;
        }

        return ReviewToken::create([
            'token' => ReviewToken::generateToken(),
            'visitor_id' => $visitor->id,
            'destination_id' => $visitor->destination_id,
            'generated_by' => Auth::id(),
            'is_used' => false,
            'expires_at' => ReviewToken::generateExpiry(),
            'created_at' => now(),
        ]);
    }

    protected static function normalizePhone(
        string $phone
    ): ?string {
        $phone = trim($phone);
        $phone = str_replace([' ', '-'], '', $phone);

        if (str_starts_with($phone, '+628')) {
            return substr($phone, 1);
        }

        if (str_starts_with($phone, '628')) {
            return $phone;
        }

        if (str_starts_with($phone, '08')) {
            return '62' . substr($phone, 1);
        }

        return null;
    }

    protected static function buildFrontendReviewUrl(
        string $token
    ): string {
        $configuredUrl = AppSettings::get(
            'public_frontend_url'
        );

        $frontendUrl = collect(
            explode(
                ',',
                (string) ($configuredUrl ?: config(
                    'app.frontend_url',
                    config('app.url')
                ))
            )
        )
            ->map(fn (string $url): string => trim($url))
            ->filter()
            ->first();

        return rtrim(
            $frontendUrl ?: config('app.url'),
            '/'
        ) . '/review/' . $token;
    }
}
