<?php

namespace App\Filament\Resources\OpdChannels\Pages;

use App\Filament\Resources\OpdChannels\OpdChannelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOpdChannels extends ListRecords
{
    protected static string $resource = OpdChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Entri Data')
        ];
    }
}
