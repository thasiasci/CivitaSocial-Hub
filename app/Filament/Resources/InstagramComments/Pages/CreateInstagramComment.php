<?php

namespace App\Filament\Resources\InstagramComments\Pages;

use App\Filament\Resources\InstagramComments\InstagramCommentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInstagramComment extends CreateRecord
{
    protected static string $resource = InstagramCommentResource::class;
    protected static ?string $title = 'Tambah Data';
    protected function getFormActions(): array{
        return[
            $this->getCreateFormAction()->label('Simpan '),
            $this->getCreateAnotherFormAction()->label('Simpan & Tambah'),
            $this->getCancelFormAction(),
        ];
    }
}
