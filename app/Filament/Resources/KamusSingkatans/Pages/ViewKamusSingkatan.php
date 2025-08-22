<?php

namespace App\Filament\Resources\KamusSingkatans\Pages;

use App\Filament\Resources\KamusSingkatans\KamusSingkatanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKamusSingkatan extends ViewRecord
{
    protected static string $resource = KamusSingkatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
