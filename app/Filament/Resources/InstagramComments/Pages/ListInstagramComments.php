<?php

namespace App\Filament\Resources\InstagramComments\Pages;

use App\Filament\Resources\InstagramComments\InstagramCommentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInstagramComments extends ListRecords
{
    protected static string $resource = InstagramCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
           //CreateAction::make()
           //->label('entri data'),
        ];
    }
}
