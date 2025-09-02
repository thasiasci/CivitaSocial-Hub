<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\KamusAnalisaWidget;
use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;

class KamusAnalisaPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $title = 'Statistik Kamus';
    protected static string | UnitEnum | null $navigationGroup = 'Kamus';

    
    protected function getHeaderWidgets(): array
    {
        return [
            KamusAnalisaWidget::class,
        ];
    }
}