<?php

namespace App\Filament\Admin\Resources\DailyVisits;

use App\Filament\Admin\Resources\DailyVisits\Pages\CreateDailyVisit;
use App\Filament\Admin\Resources\DailyVisits\Pages\EditDailyVisit;
use App\Filament\Admin\Resources\DailyVisits\Pages\ListDailyVisits;
use App\Filament\Admin\Resources\DailyVisits\Schemas\DailyVisitForm;
use App\Filament\Admin\Resources\DailyVisits\Tables\DailyVisitsTable;
use App\Models\DailyVisit;
use App\Support\UserRole;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DailyVisitResource extends Resource
{
    protected static ?string $model = DailyVisit::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedCalendar;

    protected static ?string $recordTitleAttribute =
        'date';

    protected static ?string $navigationLabel = 'Input Harian';

    protected static ?string $modelLabel = 'Input Harian';

    protected static ?string $pluralModelLabel = 'Input Harian';

    protected static ?int $navigationSort = 8;

    protected static function allowed(): bool
    {
        return in_array(
            Auth::user()?->role,
            [
                UserRole::SUPER_ADMIN,
                UserRole::ADMIN_KONTEN,
            ],
            true
        );
    }

    public static function canViewAny(): bool
    {
        return static::allowed();
    }

    public static function canView($record): bool
    {
        return static::allowed();
    }

    public static function canCreate(): bool
    {
        return static::allowed();
    }

    public static function canEdit($record): bool
    {
        return static::allowed();
    }

    public static function canDelete($record): bool
    {
        return static::allowed();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::allowed();
    }

    public static function canAccess(): bool
    {
        return static::allowed();
    }

    public static function form(Schema $schema): Schema
    {
        return DailyVisitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DailyVisitsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['destination', 'recordedBy']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDailyVisits::route('/'),
            'create' => CreateDailyVisit::route('/create'),
            'edit' => EditDailyVisit::route('/{record}/edit'),
        ];
    }
}
