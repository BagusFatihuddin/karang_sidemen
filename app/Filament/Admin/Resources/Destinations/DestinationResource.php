<?php

namespace App\Filament\Admin\Resources\Destinations;

use App\Filament\Admin\Resources\Destinations\Pages\CreateDestination;
use App\Filament\Admin\Resources\Destinations\Pages\EditDestination;
use App\Filament\Admin\Resources\Destinations\Pages\ListDestinations;
use App\Filament\Admin\Resources\Destinations\Schemas\DestinationForm;
use App\Filament\Admin\Resources\Destinations\Tables\DestinationsTable;
use App\Models\Destination;
use App\Support\UserRole;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DestinationResource extends Resource
{
    protected static ?string $model = Destination::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedMapPin;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Destinations';

    protected static ?string $modelLabel = 'Destination';

    protected static ?string $pluralModelLabel = 'Destinations';

    protected static ?int $navigationSort = 4;

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
        return DestinationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DestinationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDestinations::route('/'),
            'create' => CreateDestination::route('/create'),
            'edit' => EditDestination::route('/{record}/edit'),
        ];
    }
}