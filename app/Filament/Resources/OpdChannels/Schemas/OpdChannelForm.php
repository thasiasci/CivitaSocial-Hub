<?php

namespace App\Filament\Resources\OpdChannels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OpdChannelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('opd_name')
                    ->label('Nama Organisasi Perangkat Daerah')
                    ->required()
                    ->default(null),
                TextInput::make('youtube_username')
                    ->label('Username Youtube')
                    ->required()
                    ->default(null),
            ]);
    }
}
