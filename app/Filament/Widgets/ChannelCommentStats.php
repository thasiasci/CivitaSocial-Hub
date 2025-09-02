<?php

namespace App\Filament\Widgets;

use App\Models\KomentarUtama;
use App\Models\KomentarBalasan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Pages\Dashboard\Concerns\HasFilters;
use Carbon\CarbonImmutable;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;


class ChannelCommentStats extends BaseWidget
{
    use InteractsWithPageFilters;
    //protected static ?int $sort = 2;
    

    protected function getHeading(): ?string
    {
        return 'Analisis Data Channel';
    }

    protected function getStats(): array
    {
        // Ambil filter dari halaman dasbor
        $channelId = $this->filters['channelId'] ?? null;
        
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $queryUtama = KomentarUtama::query();
        $queryBalasan = KomentarBalasan::query();
        $queryVideo = KomentarUtama::query();

        // Terapkan filter channel jika ada
        if ($channelId) {
            $queryUtama->where('channelId', $channelId);
            $queryBalasan->where('channelId', $channelId);
            $queryVideo->where('channelId', $channelId);
        }

        // Terapkan filter tanggal jika ada
        if ($startDate && $endDate) {
            $queryUtama->whereBetween('publishedAt', [$startDate, $endDate]);
            $queryBalasan->whereBetween('publishedAt', [$startDate, $endDate]);
            $queryVideo->whereBetween('publishedAt', [$startDate, $endDate]);

        }
        $jumlahVideo = $queryVideo->distinct('videoId')->count();

        $totalKomentar = $queryUtama->count() + $queryBalasan->count();

        // penulis paling aktif komentar utama
        $penulisPalingAktif = KomentarUtama::select('authorDisplayName')
            ->when($channelId, fn ($q) => $q->where('channelId', $channelId))
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('publishedAt', [$startDate, $endDate]))
            ->groupBy('authorDisplayName')
            ->orderByRaw('COUNT(*) DESC')
            ->first();

        return [
            Stat::make('Total Video',number_format ($jumlahVideo))
                ->description('Jumlah video dalam Channel'),
            Stat::make('Total Komentar', number_format($totalKomentar))
                ->description('Jumlah Komentar dalam Channel'),
            Stat::make('Pemberi Komentar Terbanyak', $penulisPalingAktif ? $penulisPalingAktif->authorDisplayName : 'Tidak ditemukan')
                ->description('Kontributor Komentar Utama'),
            
        ];
    }
}