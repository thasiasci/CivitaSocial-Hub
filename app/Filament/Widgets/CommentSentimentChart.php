<?php

namespace App\Filament\Widgets;

use App\Models\KomentarUtama;
use App\Models\KomentarBalasan;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class CommentSentimentChart extends ChartWidget
{
    use InteractsWithPageFilters; 

    protected static ?int $sort = 4;
    protected ?string $heading = 'Distribusi Sentimen Komentar';

    protected function getType(): string
    {
        return 'pie'; 
    }

   protected function getData(): array
    {
        $channelId = $this->filters['channelId'] ?? null;
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $utamaQuery = KomentarUtama::query();
        $balasanQuery = KomentarBalasan::query();
        
        if ($channelId) {
            $utamaQuery->where('channelId', $channelId);
            $balasanQuery->where('channelId', $channelId);
        }

        if ($startDate && $endDate) {
            $utamaQuery->whereBetween('publishedAt', [$startDate, $endDate]);
            $balasanQuery->whereBetween('publishedAt', [$startDate, $endDate]);
        }

        $utamaLabeled = $utamaQuery->whereNotNull('sentimen')->select('sentimen')->selectRaw('count(*) as count')->groupBy('sentimen')->get();
        $balasanLabeled = $balasanQuery->whereNotNull('sentimen')->select('sentimen')->selectRaw('count(*) as count')->groupBy('sentimen')->get();
        
        $mergedLabeledData = $utamaLabeled->concat($balasanLabeled)->groupBy('sentimen')->map(fn ($row) => $row->sum('count'));
        
        $colors = [
            'Positif' => '#22c55e',
            'Negatif' => '#ef4444',
            'Netral' => '#64748b',
        ];

        $labels = $mergedLabeledData->keys()->toArray();
        $data = $mergedLabeledData->values()->toArray();
        $backgroundColors = array_map(fn ($label) => $colors[$label] ?? '#d1d5db', $labels);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Komentar',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 12, 
                        'boxWidth' => 12,
                        'boxHeight' => 12,
                        'usePointStyle' => true,
                        'font' => [
                            'size' => 12, 
                        ],
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
            'responsive' => true,
            'layout' => [
                'padding' => [
                    'top' => 10,     
                    'right' => 15,     
                    'bottom' => 40,  
                    'left' => 15,    
                ],
            ],
            'aspectRatio' => 1.3, 
        ];
    }
}