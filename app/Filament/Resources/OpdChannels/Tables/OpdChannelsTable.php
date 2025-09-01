<?php

namespace App\Filament\Resources\OpdChannels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
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
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->modalHeading('Hapus OPD Channel') 
                        ->modalDescription('Apakah kamu yakin ingin menghapus data OPD yang terpilih?')
                        ->modalSubmitActionLabel('Ya, Hapus') 
                        ->modalCancelActionLabel('Batal'),
                ])
                ->label('Aksi')
                ->size('sm')
                ->color('gray')
                ->button(),
            ])
        
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                
            ]);
    }
}