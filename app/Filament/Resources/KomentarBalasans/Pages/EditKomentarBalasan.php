<?php

namespace App\Filament\Resources\KomentarBalasans\Pages;

use App\Filament\Resources\KomentarBalasans\KomentarBalasanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditKomentarBalasan extends EditRecord
{
    protected static string $resource = KomentarBalasanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
