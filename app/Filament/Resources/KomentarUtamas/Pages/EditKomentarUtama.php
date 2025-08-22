<?php

namespace App\Filament\Resources\KomentarUtamas\Pages;

use App\Filament\Resources\KomentarUtamas\KomentarUtamaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditKomentarUtama extends EditRecord
{
    protected static string $resource = KomentarUtamaResource::class;
     protected static ?string $title = 'Beri Label Sentimen';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
