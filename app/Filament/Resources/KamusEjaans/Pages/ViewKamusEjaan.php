<?php

namespace App\Filament\Resources\KamusEjaans\Pages;

use App\Filament\Resources\KamusEjaans\KamusEjaanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKamusEjaan extends ViewRecord
{
    protected static string $resource = KamusEjaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
