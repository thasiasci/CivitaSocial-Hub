<?php

namespace App\Filament\Resources\KamusSingkatans\Pages;

use App\Filament\Resources\KamusSingkatans\KamusSingkatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKamusSingkatans extends ListRecords
{
    protected static string $resource = KamusSingkatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label("Entri Data"),
        ];
    }
}
