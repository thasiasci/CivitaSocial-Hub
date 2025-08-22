<?php

namespace App\Filament\Widgets;

use App\Models\KomentarUtama;
use Filament\Widgets\ChartWidget;
use Filament\Pages\Dashboard\Concerns\HasFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class CommentsPerDayChart extends ChartWidget
{
     use InteractsWithPageFilters; 
protected ?string $heading = 'Tren Komentar Per Tahun';
    
    protected static ?int $sort = 3;

    protected function getType(): string
    {
        return 'line'; // Ini akan membuat grafik garis
    }

    protected function getData(): array
    {
        // Ambil filter dari halaman dasbor
        $channelId = $this->filters['channelId'] ?? null;
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $query = KomentarUtama::query();

        // Terapkan filter channel jika ada
        if ($channelId) {
            $query->where('channelId', $channelId);
        }

        // Terapkan filter tanggal jika ada
        if ($startDate && $endDate) {
            $query->whereBetween('publishedAt', [$startDate, $endDate]);
        }

        // Kelompokkan komentar per hari
        $dataPerYear = $query->selectRaw('YEAR(publishedAt) as year, count(*) as count')
            ->groupBy('year')
            ->orderBy('year')
            ->get();
        
        return [
            'labels' => $dataPerYear->pluck('year'),
            'datasets' => [
                [
                    'label' => 'Jumlah Komentar',
                    'data' => $dataPerYear->pluck('count'),
                    'backgroundColor' => 'transparent',
                    'borderColor' => '#3b82f6',
                    'fill' => true,
                    'pointRadius' => 5,
                ],
            ],
        ];
    }
}