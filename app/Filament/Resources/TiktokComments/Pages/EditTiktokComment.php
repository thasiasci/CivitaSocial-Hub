<?php

namespace App\Filament\Resources\TiktokComments\Pages;

use App\Filament\Resources\TiktokComments\TiktokCommentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTiktokComment extends EditRecord
{
    protected static string $resource = TiktokCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
