<?php

namespace App\Filament\Resources\KamusEjaans\Pages;

use App\Filament\Resources\KamusEjaans\KamusEjaanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKamusEjaans extends ListRecords
{
    protected static string $resource = KamusEjaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Entri Data')
        ];
    }
}
