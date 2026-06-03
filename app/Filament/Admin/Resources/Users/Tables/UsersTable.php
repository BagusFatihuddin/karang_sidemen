<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Models\User;
use App\Support\UserRole;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => UserRole::label($state)
                    )
                    ->color(fn (string $state): string => match ($state) {
                        UserRole::SUPER_ADMIN => 'danger',
                        UserRole::ADMIN_KONTEN => 'info',
                        UserRole::PIMPINAN => 'success',
                        UserRole::ANGGOTA_POKDARWIS => 'warning',
                        UserRole::PETUGAS_LAPANGAN => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),

                DeleteAction::make()
                    ->visible(
                        fn (User $record): bool =>
                            Auth::id() !== $record->id
                    ),
            ]);
    }
}