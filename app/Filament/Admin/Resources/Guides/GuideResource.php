<?php

namespace App\Filament\Admin\Resources\Guides;

use App\Filament\Admin\Resources\Guides\Pages\CreateGuide;
use App\Filament\Admin\Resources\Guides\Pages\EditGuide;
use App\Filament\Admin\Resources\Guides\Pages\ListGuides;
use App\Filament\Admin\Resources\Guides\Schemas\GuideForm;
use App\Filament\Admin\Resources\Guides\Tables\GuidesTable;
use App\Models\Guide;
use App\Support\UserRole;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class GuideResource extends Resource
{
    protected static ?string $model = Guide::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedIdentification;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Guide Lokal';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten Wisata';

    protected static ?string $modelLabel = 'Guide';

    protected static ?string $pluralModelLabel = 'Guide Lokal';

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
        return GuideForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuidesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGuides::route('/'),
            'create' => CreateGuide::route('/create'),
            'edit' => EditGuide::route('/{record}/edit'),
        ];
    }
}
