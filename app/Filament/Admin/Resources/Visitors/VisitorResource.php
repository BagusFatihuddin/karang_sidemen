<?php

namespace App\Filament\Admin\Resources\Visitors;

use App\Filament\Admin\Resources\Visitors\Infolists\VisitorInfolist;
use App\Filament\Admin\Resources\Visitors\Pages\ListVisitors;
use App\Filament\Admin\Resources\Visitors\Pages\ViewVisitor;
use App\Filament\Admin\Resources\Visitors\Tables\VisitorsTable;
use App\Models\Visitor;
use App\Support\UserRole;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VisitorResource extends Resource
{
    protected static ?string $model = Visitor::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Wisatawan';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengunjung';

    protected static ?string $modelLabel = 'Wisatawan';

    protected static ?string $pluralModelLabel = 'Wisatawan';

    protected static ?int $navigationSort = 1;

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

    public static function shouldRegisterNavigation(): bool
    {
        return static::allowed();
    }

    public static function canAccess(): bool
    {
        return static::allowed();
    }

    public static function table(Table $table): Table
    {
        return VisitorsTable::configure($table);
    }

    public static function infolist(
        Schema $schema
    ): Schema {
        return VisitorInfolist::configure($schema);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'destination',
                'recordedBy',
                'reviewTokens',
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVisitors::route('/'),
            'view' => ViewVisitor::route('/{record}'),
        ];
    }
}
