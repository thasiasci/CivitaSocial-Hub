<?php

namespace App\Filament\Resources\InstagramComments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class InstagramCommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('authorProfileUrl')
                    ->default(null),
                TextInput::make('authorProfileImageUrl')
                    ->default(null),
                TextInput::make('authorDisplayName')
                    ->default(null),
                TextInput::make('commentUrl')
                    ->default(null),
                DateTimePicker::make('publishedAt'),
                Textarea::make('comment')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('likeCount')
                    ->numeric()
                    ->default(null),
                TextInput::make('replyCount')
                    ->default(null),
            ]);
    }
}
