<?php

namespace App\Filament\Resources\OpdChannels\Pages;

use App\Filament\Resources\OpdChannels\OpdChannelResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOpdChannel extends CreateRecord
{
    protected static string $resource = OpdChannelResource::class;
    protected static ?string $title = 'Tambah Data OPD Channel';

    protected function getFormActions():array{
        return[
            $this->getCreateFormAction()->label('Simpan'),
            $this->getCreateAnotherFormAction()->label('Simpan & Tambah'),
            $this->getCancelFormAction()
        ];
    }
    protected function getRedirectUrl():string{
        return static::getResource()::getUrl('index');
    }
}
