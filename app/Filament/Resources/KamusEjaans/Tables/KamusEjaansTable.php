<?php

namespace App\Filament\Resources\KamusEjaans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KamusEjaansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("ejaan_salah"),
                TextColumn::make("ejaan_benar")
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->modalHeading('Hapus Ejaan') 
                    ->modalDescription('Apakah kamu yakin ingin menghapus ejaan yang terpilih?') // <-- Ubah deskripsi
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
