<?php

namespace App\Filament\Resources\KomentarUtamas\Pages;

use App\Filament\Resources\KomentarUtamas\KomentarUtamaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKomentarUtamas extends ListRecords
{
    protected static string $resource = KomentarUtamaResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // CreateAction::make(),
        ];
    }
}
