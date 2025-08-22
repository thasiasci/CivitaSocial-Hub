<?php

namespace App\Filament\Resources\KamusEjaans\Pages;

use App\Filament\Resources\KamusEjaans\KamusEjaanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditKamusEjaan extends EditRecord
{
    protected static string $resource = KamusEjaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getFormActions():array{
        return[
            $this->getSaveFormAction()->label('Simpan Perubahan'),
            $this->getCancelFormAction(),
        ];
    }
     protected function getRedirectUrl():string{
        return static::getResource()::getUrl('index');
    }
}
