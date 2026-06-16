<?php

namespace App\Filament\Admin\Resources\Promos;

use App\Filament\Admin\Resources\Promos\Pages\CreatePromo;
use App\Filament\Admin\Resources\Promos\Pages\EditPromo;
use App\Filament\Admin\Resources\Promos\Pages\ListPromos;
use App\Filament\Admin\Resources\Promos\Schemas\PromoForm;
use App\Filament\Admin\Resources\Promos\Tables\PromosTable;
use App\Models\Promo;
use App\Support\UserRole;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PromoResource extends Resource
{
    protected static ?string $model = Promo::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedMegaphone;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'Events';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten Wisata';

    protected static ?string $modelLabel = 'Event';

    protected static ?string $pluralModelLabel = 'Events';

    protected static ?int $navigationSort = 3;

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
        return PromoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PromosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromos::route('/'),
            'create' => CreatePromo::route('/create'),
            'edit' => EditPromo::route('/{record}/edit'),
        ];
    }
}
