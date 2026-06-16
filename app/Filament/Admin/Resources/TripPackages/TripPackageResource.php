<?php

namespace App\Filament\Admin\Resources\TripPackages;

use App\Filament\Admin\Resources\TripPackages\Pages\CreateTripPackage;
use App\Filament\Admin\Resources\TripPackages\Pages\EditTripPackage;
use App\Filament\Admin\Resources\TripPackages\Pages\ListTripPackages;
use App\Filament\Admin\Resources\TripPackages\Schemas\TripPackageForm;
use App\Filament\Admin\Resources\TripPackages\Tables\TripPackagesTable;
use App\Models\TripPackage;
use App\Support\UserRole;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TripPackageResource extends Resource
{
    protected static ?string $model = TripPackage::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedMap;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Paket Wisata';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten Wisata';

    protected static ?string $modelLabel = 'Paket Wisata';

    protected static ?string $pluralModelLabel = 'Paket Wisata';

    protected static ?int $navigationSort = 2;

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
        return TripPackageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TripPackagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTripPackages::route('/'),
            'create' => CreateTripPackage::route('/create'),
            'edit' => EditTripPackage::route('/{record}/edit'),
        ];
    }
}
