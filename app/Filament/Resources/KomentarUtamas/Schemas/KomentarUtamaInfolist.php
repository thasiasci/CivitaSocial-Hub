<?php

namespace App\Filament\Resources\KomentarUtamas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class KomentarUtamaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('channelId'),
                TextEntry::make('videoId'),
                TextEntry::make('title'),
                TextEntry::make('authorDisplayName'),
                TextEntry::make('authorProfileImageUrl'),
                TextEntry::make('authorChannelUrl'),
                TextEntry::make('likeCount')
                    ->numeric(),
                TextEntry::make('publishedAt')
                    ->dateTime(),
                TextEntry::make('totalReplyCount')
                    ->numeric(),
            ]);
    }
}
