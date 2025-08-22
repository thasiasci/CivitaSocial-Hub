<?php

namespace App\Filament\Resources\KomentarBalasans\Pages;

use App\Filament\Resources\KomentarBalasans\KomentarBalasanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKomentarBalasan extends ViewRecord
{
    protected static string $resource = KomentarBalasanResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // EditAction::make(),
        ];
    }
}
