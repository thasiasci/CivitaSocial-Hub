<?php

namespace App\Filament\Resources\KomentarBalasans\Pages;

use App\Filament\Resources\KomentarBalasans\KomentarBalasanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKomentarBalasans extends ListRecords
{
    protected static string $resource = KomentarBalasanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make()
               // ->label("Entri Data"),
        ];
    }
}
