<?php

namespace App\Filament\Resources\KomentarBalasans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class KomentarBalasanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('parent_comment_id'),
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
