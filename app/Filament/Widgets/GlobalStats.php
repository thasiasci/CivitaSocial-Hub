<?php

namespace App\Filament\Widgets;

use App\Models\OpdChannel;
use App\Models\KomentarUtama;
use App\Models\KomentarBalasan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class GlobalStats extends BaseWidget
{
    protected function getHeading(): ?string
    {
        return 'Ringkasan Statistik Global';
    }
    //protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalChannels = OpdChannel::count();
        $channelsWithId = OpdChannel::whereNotNull('channel_id')->where('channel_id', '!=', '')->count();
        $totalKomentar = KomentarUtama::count();
        $totalKomentarBalasan = KomentarBalasan::count();

        return [
            Stat::make('Koneksi Channel', "{$channelsWithId}/{$totalChannels}")
                ->description(new HtmlString("<strong>Sudah Terdaftar dengan ID:</strong> {$channelsWithId} channel<br><strong>Total channel di database:</strong> {$totalChannels} channel")),
            stat::make('Jumlah Komentar Utama', number_format ($totalKomentar))
                ->description(' Komentar Utama seluruh Channel.'),
            stat::make(' Jumlah Komentar Balasan',  number_format($totalKomentarBalasan))
                ->description(' Komentar Balasan seluruh Channel.'),
        ];
    }
}