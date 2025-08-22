<?php

namespace App\Filament\Resources\OpdChannels\Pages;

use App\Filament\Resources\OpdChannels\OpdChannelResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditOpdChannel extends EditRecord
{
    protected static string $resource = OpdChannelResource::class;

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
