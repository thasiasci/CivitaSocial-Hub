<?php

namespace App\Filament\Resources\KamusEjaans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KamusEjaanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make("ejaan_salah")
                    ->label('Ejaan Salah')
                    ->required()
                    ->unique(),
                TextInput::make("ejaan_benar")
                    ->label('Ejaan Benar')
                    ->required()
            ]);
    }
}
