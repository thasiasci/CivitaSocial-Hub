<?php

namespace App\Filament\Resources\KamusSingkatans\Pages;

use App\Filament\Resources\KamusSingkatans\KamusSingkatanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKamusSingkatan extends CreateRecord
{
    protected static string $resource = KamusSingkatanResource::class;
    protected static ?string $title = 'Tambah Data Singkatan';
    protected function getFormActions():array
    {
        return[
            $this->getCreateFormAction()->label('Simpan'),
            $this->getCreateAnotherFormAction()->label('Simpan & Tambah' ),
            $this->getCancelFormAction(),
        ];
    }

     protected function getRedirectUrl():string{
        return static::getResource()::getUrl('index');
    }
}
