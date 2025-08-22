<?php

namespace App\Filament\Resources\OpdChannels\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OpdChannelInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('channel_id'),
                TextEntry::make('opd_name'),
                TextEntry::make('youtube_username'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
