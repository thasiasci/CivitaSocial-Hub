<?php

namespace App\Filament\Widgets;

use App\Models\OpdChannel;
use App\Models\KomentarUtama;
use App\Models\KomentarBalasan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GlobalStats extends BaseWidget
{
    protected function getHeading(): ?string
    {
        return 'Ringkasan Statistik Global';
    }

    protected function getStats(): array
    {
        $totalChannels = OpdChannel::count();
        $totalKomentar = KomentarUtama::count();
        $totalKomentarBalasan = KomentarBalasan::count();

        return [
            Stat::make('Total Channel', $totalChannels)
                ->description(' Seluruh Channel yang sudah terdaftar.'),
            stat::make('Jumlah Komentar Utama', $totalKomentar)
                ->description(' Komentar Utama seluruh Channel.'),
            stat::make(' Jumlah Komentar Balasan', $totalKomentarBalasan)
                ->description(' Komentar Balasan seluruh Channel.'),
        ];
    }
}