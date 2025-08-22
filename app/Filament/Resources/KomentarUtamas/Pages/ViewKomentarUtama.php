<?php

namespace App\Filament\Resources\KomentarUtamas\Pages;

use App\Filament\Resources\KomentarUtamas\KomentarUtamaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKomentarUtama extends ViewRecord
{
    protected static string $resource = KomentarUtamaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //EditAction::make(),
        ];
    }
}
