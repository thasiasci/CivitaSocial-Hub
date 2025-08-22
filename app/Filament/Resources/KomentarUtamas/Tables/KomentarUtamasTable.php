<?php

namespace App\Filament\Resources\KomentarUtamas\Tables;

use App\Models\KomentarUtama;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Form; 
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class KomentarUtamasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('opdChannel.opd_name')
                    ->label('Nama OPD')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Judul Video')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('textOriginal')
                    ->label('Komentar')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('authorDisplayName')
                    ->label('Username Penulis')
                    ->searchable(),
                TextColumn::make('likeCount')
                    ->label('Jumlah Like')
                    ->numeric()
                    ->sortable(),
                 TextColumn::make('totalReplyCount')
                    ->label('Jumlah Balasan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('publishedAt')
                    ->label('Tanggal Komentar')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('sentimen') 
                    ->label('Sentimen')
                    ->sortable()
                    ->badge(),
               
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make('labelSentimen')
                    ->label('Beri Label Sentimen')
                    ->modalHeading('Beri Label Sentimen')
                    ->modalSubmitActionLabel('Simpan') 
                    ->modalCancelActionLabel()
                    ->form([
                        Textarea::make('textOriginal')
                            ->label('Isi Komentar Utama')
                            ->disabled()
                            ->columnSpanFull(),
                        Select::make('sentimen')
                            ->options([
                                'Positif' => 'Positif',
                                'Negatif' => 'Negatif',
                                'Netral' => 'Netral',
                            ])
                            ->label('Sentimen')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data, KomentarUtama $record): void {
                        $record->sentimen = $data['sentimen'];
                        $record->save();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
