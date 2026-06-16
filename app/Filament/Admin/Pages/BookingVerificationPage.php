<?php

namespace App\Filament\Admin\Pages;

use App\Models\Booking;
use App\Support\UserRole;
use BackedEnum;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class BookingVerificationPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view =
        'filament.admin.pages.booking-verification-page';

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedCheckCircle;

    protected static ?string $navigationLabel =
        'Verifikasi Booking';

    protected static string|\UnitEnum|null $navigationGroup = 'Operasional';

    protected static ?string $title =
        'Verifikasi Booking';

    protected static ?string $slug =
        'booking-verification';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public ?Booking $booking = null;

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->role === UserRole::PETUGAS_LAPANGAN,
            403
        );

        $this->form->fill([]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->role ===
            UserRole::PETUGAS_LAPANGAN;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->role ===
            UserRole::PETUGAS_LAPANGAN;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Cari Booking')
                    ->description('Masukkan kode booking untuk memverifikasi kedatangan wisatawan.')
                    ->schema([
                        TextInput::make('booking_code')
                            ->label('Kode Booking')
                            ->placeholder('Contoh: KS-12345')
                            ->required()
                            ->maxLength(10)
                            ->afterStateUpdated(
                                fn () => $this->searchBooking()
                            )
                            ->live(debounce: 500),
                    ])
                    ->columns(1),

                Section::make('Detail Booking')
                    ->visible(fn () => $this->booking !== null)
                    ->schema([
                        Placeholder::make('booking_code')
                            ->label('Kode Booking')
                            ->content(fn () => $this->booking?->booking_code),

                        Placeholder::make('guest_name')
                            ->label('Nama Wisatawan')
                            ->content(
                                fn () => $this->booking?->visitor?->name
                                    ?? $this->booking?->guest_name
                            ),

                        Placeholder::make('destination')
                            ->label('Destinasi')
                            ->content(
                                fn () => $this->booking?->destination?->name
                            ),

                        Placeholder::make('checkin_date')
                            ->label('Tanggal Check-in')
                            ->content(
                                fn () => $this->booking?->checkin_date?->format('d-m-Y')
                            ),

                        Placeholder::make('checkout_date')
                            ->label('Tanggal Check-out')
                            ->content(
                                fn () => $this->booking?->checkout_date?->format('d-m-Y')
                                    ?? '—'
                            ),

                        Placeholder::make('status')
                            ->label('Status')
                            ->content(
                                fn () => match ($this->booking?->status) {
                                    'pending' => 'Menunggu',
                                    'confirmed' => 'Terkonfirmasi',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Dibatalkan',
                                    default => $this->booking?->status,
                                }
                            ),
                    ])
                    ->columns(1),
            ]);
    }

    public function searchBooking(): void
    {
        $code = strtoupper(trim($this->data['booking_code'] ?? ''));

        if (blank($code)) {
            $this->booking = null;
            return;
        }

        $this->booking = Booking::query()
            ->with(['visitor', 'destination'])
            ->where('booking_code', $code)
            ->first();

        if ($this->booking === null) {
            Notification::make()
                ->title('Kode booking tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        if ($this->booking->status === 'completed') {
            Notification::make()
                ->title('Sudah diverifikasi')
                ->body('Booking ini sudah diverifikasi pada ' . $this->booking->arrived_at?->format('d-m-Y H:i'))
                ->warning()
                ->send();
            return;
        }

        if ($this->booking->status === 'cancelled') {
            Notification::make()
                ->title('Booking dibatalkan')
                ->body('Booking ini telah dibatalkan dan tidak dapat diverifikasi.')
                ->danger()
                ->send();
            $this->booking = null;
            return;
        }
    }

    public function verify(): void
    {
        if ($this->booking === null) {
            Notification::make()
                ->title('Tidak ada booking untuk diverifikasi')
                ->danger()
                ->send();
            return;
        }

        if (! in_array($this->booking->status, ['pending', 'confirmed'])) {
            Notification::make()
                ->title('Booking tidak dapat diverifikasi')
                ->danger()
                ->send();
            return;
        }

        $this->booking->update([
            'status' => 'completed',
            'arrived_at' => now(),
        ]);

        Notification::make()
            ->title('Verifikasi berhasil')
            ->body('Wisatawan berhasil dicatat sebagai sudah datang.')
            ->success()
            ->send();

        $this->booking = null;
        $this->data = [];
    }
}
