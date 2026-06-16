<?php

namespace App\Filament\Admin\Pages;

use App\Models\Destination;
use App\Models\Visitor;
use App\Support\UserRole;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class VisitorRegistrationPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view =
        'filament.admin.pages.visitor-registration-page';

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedUserPlus;

    protected static ?string $navigationLabel =
        'Registrasi Wisatawan';

    protected static string|\UnitEnum|null $navigationGroup = 'Operasional';

    protected static ?string $title =
        'Registrasi Wisatawan';

    protected static ?string $slug =
        'visitor-registration';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->role === UserRole::PETUGAS_LAPANGAN,
            403
        );

        $this->form->fill(
            $this->defaultFormData()
        );
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
                Section::make('Data Wisatawan')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->rule(
                                'regex:/^(\+628|628|08)[0-9]{8,12}$/'
                            ),

                        Select::make('origin_category')
                            ->label('Kategori Asal')
                            ->options([
                                'lombok_tengah' => 'Lombok Tengah',
                                'lombok_lainnya' => 'Lombok Lainnya',
                                'luar_lombok' => 'Luar Lombok',
                                'mancanegara' => 'Mancanegara',
                            ])
                            ->required()
                            ->native(false),

                        TextInput::make('origin_city')
                            ->label('Kota Asal')
                            ->required()
                            ->maxLength(100),

                        Select::make('visit_type')
                            ->label('Tipe Kunjungan')
                            ->options([
                                'sendiri' => 'Sendiri',
                                'pasangan' => 'Pasangan',
                                'keluarga' => 'Keluarga',
                                'rombongan' => 'Rombongan',
                            ])
                            ->required()
                            ->native(false),

                        TextInput::make('group_size')
                            ->label('Jumlah Grup')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),

                        Select::make('destination_id')
                            ->label('Destinasi')
                            ->options(
                                fn (): array => Destination::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all()
                            )
                            ->searchable()
                            ->required()
                            ->native(false),

                        Select::make('referral_source')
                            ->label('Sumber Tahu')
                            ->options([
                                'instagram' => 'Instagram',
                                'whatsapp' => 'WhatsApp',
                                'teman' => 'Teman',
                                'google' => 'Google',
                                'lainnya' => 'Lainnya',
                            ])
                            ->live()
                            ->required()
                            ->native(false),

                        TextInput::make('referral_other')
                            ->label('Sumber Lainnya')
                            ->maxLength(100)
                            ->visible(
                                fn (Get $get): bool =>
                                $get('referral_source') === 'lainnya'
                            ),

                        DateTimePicker::make('visited_at')
                            ->label('Tanggal Kunjungan')
                            ->default(now())
                            ->required(),
                    ])
                    ->columns(1),
            ]);
    }

    public function register(): void
    {
        $data = $this->form->getState();

        if (($data['referral_source'] ?? null) !== 'lainnya') {
            $data['referral_other'] = null;
        }

        $data['recorded_by'] = Auth::id();

        Visitor::create($data);

        Notification::make()
            ->title('Data wisatawan berhasil disimpan.')
            ->success()
            ->send();

        $this->form->fill(
            $this->defaultFormData()
        );
    }

    protected function defaultFormData(): array
    {
        return [
            'group_size' => 1,
            'visited_at' => now(),
        ];
    }
}
