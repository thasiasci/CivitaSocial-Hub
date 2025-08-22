<?php

namespace App\Filament\Resources\OpdChannels\Pages;

use App\Filament\Resources\OpdChannels\OpdChannelResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOpdChannel extends ViewRecord
{
    protected static string $resource = OpdChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
