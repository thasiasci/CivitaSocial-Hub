<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use App\Filament\Widgets\InstagramOverview;
use App\Filament\Widgets\MonthlyTrendChart;
use App\Filament\Widgets\SentimentAnalysisWidget;
use UnitEnum;

class InstagramDashboard extends Page
{
    protected string $view = 'filament.pages.instagram-dashboard';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home'; 
    protected static ?string $navigationLabel = 'Dashboard Instagram';
    protected static string | UnitEnum | null $navigationGroup = 'Dashboard';
    protected function getHeaderWidgets(): array
    {
        return [
            InstagramOverview::class, 
            MonthlyTrendChart::class,
            SentimentAnalysisWidget::class,
        ];
    }
}
