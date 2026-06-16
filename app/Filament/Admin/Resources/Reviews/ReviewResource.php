<?php

namespace App\Filament\Admin\Resources\Reviews;

use App\Filament\Admin\Resources\Reviews\Infolists\ReviewInfolist;
use App\Filament\Admin\Resources\Reviews\Pages\ListReviews;
use App\Filament\Admin\Resources\Reviews\Pages\ViewReview;
use App\Filament\Admin\Resources\Reviews\Tables\ReviewsTable;
use App\Models\Review;
use App\Support\UserRole;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $recordTitleAttribute = 'reviewer_name';

    protected static ?string $navigationLabel = 'Reviews';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengunjung';

    protected static ?string $modelLabel = 'Review';

    protected static ?string $pluralModelLabel = 'Review';

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

    public static function canView($record): bool
    {
        return static::allowed();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->role === UserRole::SUPER_ADMIN;
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
        return ReviewsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReviewInfolist::configure($schema);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'destination',
                'visitor',
                'approvedBy',
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'view' => ViewReview::route('/{record}'),
        ];
    }
}
