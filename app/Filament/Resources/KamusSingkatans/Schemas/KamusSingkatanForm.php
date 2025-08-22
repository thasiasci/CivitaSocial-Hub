<?php

namespace App\Filament\Resources\KamusSingkatans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KamusSingkatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('singkatan')
                    ->label('Singkatan')
                    ->required()
                    ->unique()
                    ->default(null),
                TextInput::make('kepanjangan')
                    ->label('Kepanjangan')
                    ->required()
                    ->default(null),
            ]);
    }
}
