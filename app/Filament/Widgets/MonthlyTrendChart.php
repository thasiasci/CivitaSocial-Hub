<?php


namespace App\Filament\Widgets;

use App\Models\MonthlyInstagramStats;
use Filament\Widgets\ChartWidget;

class MonthlyTrendChart extends ChartWidget
{
    protected ?string $heading = 'Tren Aktivitas Bulanan';
    protected int | string | array $columnSpan = 'full';
    
    

    protected function getData(): array
    {
        $monthlyData = MonthlyInstagramStats::all();
        
        return [
            'datasets' => [
                [
                    'label' => 'Total Komentar',
                    'data' => $monthlyData->pluck('total_comments')->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Konten ',
                    'data' => $monthlyData->pluck('unique_posts')->toArray(),
                    'borderColor' => '#ffff00ff',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Spam Terdeteksi',
                    'data' => $monthlyData->pluck('spam_count')->toArray(),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Sentimen Positif',
                    'data' => $monthlyData->pluck('positive_count')->toArray(),
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Sentimen Negatif',
                    'data' => $monthlyData->pluck('negative_count')->toArray(),
                    'borderColor' => '#b700ffff',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $monthlyData->pluck('bulan')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
        ];
    }
}