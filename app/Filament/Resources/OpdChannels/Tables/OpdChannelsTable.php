<?php

namespace App\Filament\Resources\OpdChannels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OpdChannelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('opd_name')
                    ->label('Nama Organisasi Perangkat Daerah')
                    ->searchable(),
                TextColumn::make('youtube_username')
                    ->label('Username Youtube')
                    ->searchable(),
                TextColumn::make('channel_id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->modalHeading('Hapus Ejaan') 
                    ->modalDescription('Apakah kamu yakin ingin menghapus data OPD yang terpilih?') // <-- Ubah deskripsi
                    ->modalSubmitActionLabel('Ya, Hapus') 
                    ->modalCancelActionLabel(), 
            ])
        
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
