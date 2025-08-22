<?php

namespace App\Filament\Resources\KomentarUtamas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class KomentarUtamaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('channelId')
                    ->default(null),
                TextInput::make('videoId')
                    ->default(null),
                TextInput::make('title')
                    ->default(null),
                Textarea::make('textOriginal')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('textCleaned')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('authorDisplayName')
                    ->default(null),
                TextInput::make('authorProfileImageUrl')
                    ->default(null),
                TextInput::make('authorChannelUrl')
                    ->default(null),
                TextInput::make('likeCount')
                    ->numeric()
                    ->default(null),
                DateTimePicker::make('publishedAt'),
                TextInput::make('totalReplyCount')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
