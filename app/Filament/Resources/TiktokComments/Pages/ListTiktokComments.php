<?php

namespace App\Filament\Resources\TiktokComments\Pages;

use App\Filament\Resources\TiktokComments\TiktokCommentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTiktokComments extends ListRecords
{
    protected static string $resource = TiktokCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
