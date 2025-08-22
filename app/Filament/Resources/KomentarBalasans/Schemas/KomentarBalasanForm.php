<?php

namespace App\Filament\Resources\KomentarBalasans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select; 

class KomentarBalasanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('textOriginal')
                    ->label('Isi Komentar')
                    ->disabled()
                    ->columnSpanFull(),
                Select::make('sentimen') // Ini field baru kita
                    ->options([
                        'Positif' => 'Positif',
                        'Negatif' => 'Negatif',
                        'Netral' => 'Netral',
                    ])
                    ->label('Sentimen')
                    ->placeholder('Pilih Sentimen')
                    ->columnSpanFull(),
                
            ]);
    }
}
