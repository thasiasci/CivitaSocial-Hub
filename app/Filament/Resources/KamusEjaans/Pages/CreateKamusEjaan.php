<?php

namespace App\Filament\Resources\KamusEjaans\Pages;

use App\Filament\Resources\KamusEjaans\KamusEjaanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKamusEjaan extends CreateRecord
{
    protected static string $resource = KamusEjaanResource::class;
    protected static ?string $title = 'Tambah Data Kamus Ejaan';
    protected function getFormActions():array
    {
        return[
            $this->getCreateFormAction()->label('Simpan'),
            $this->getCreateAnotherFormAction()->label('Simpan & Tambah'),
            $this->getCancelFormAction(),
        ];
    }
     protected function getRedirectUrl():string{
        return static::getResource()::getUrl('index');
    }
}
