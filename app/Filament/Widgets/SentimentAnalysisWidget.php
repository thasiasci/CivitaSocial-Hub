<?php

namespace App\Filament\Widgets;

use App\Models\SentimentDistributionStats;
use Filament\Widgets\ChartWidget;

class SentimentAnalysisWidget extends ChartWidget
{
    protected int | string | array $columnSpan = 'full';
    protected ?string $heading = 'Distribusi Sentimen';
    
    public ?string $filter = 'all';
    
    protected function getFilters(): ?array
    {
        $months = SentimentDistributionStats::select('bulan')->distinct()->pluck('bulan', 'bulan')->toArray();
        return array_merge(['all' => 'Semua Bulan'], $months);
    }

    protected function getData(): array
    {
        $query = SentimentDistributionStats::query();
        
        if ($this->filter && $this->filter !== 'all') {
            $query->where('bulan', $this->filter);
        }
        
        $positive = $query->clone()->where('sentimen', 'Positif')->count();
        $neutral = $query->clone()->where('sentimen', 'Netral')->count(); 
        $negative = $query->clone()->where('sentimen', 'Negatif')->count();
        
        return [
            'datasets' => [
                [
                    'data' => [$positive, $neutral, $negative],
                    'backgroundColor' => ['#abd1c6', '#f9bc60', '#e16162'],
                    'borderWidth' => 2,
                    'borderColor' => '#e8e4e6',
                ],
            ],
            'labels' => ['Positif', 'Netral', 'Negatif'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                        'font' => ['size' => 14],
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => 'white',
                    'bodyColor' => 'white',
                    'borderColor' => 'rgba(255, 255, 255, 0.1)',
                    'borderWidth' => 1,
                    'cornerRadius' => 8,
                ],
            ],
            'cutout' => '60%',
        ];
    }
}