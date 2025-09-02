<?php

namespace App\Filament\Resources\InstagramComments\Pages;

use App\Filament\Resources\InstagramComments\InstagramCommentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInstagramComment extends EditRecord
{
    protected static string $resource = InstagramCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
