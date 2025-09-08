<?php

namespace App\Filament\Widgets;

use App\Models\InstagramComment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;


class InstagramOverview extends StatsOverviewWidget
{
    
    protected function getStats(): array
    {
        $totalKomentar = InstagramComment::count();
        $totalKonten = InstagramComment::distinct('link_konten')->count();
        $totalSpam = InstagramComment::where('is_spam', true)->count();
        $spamPercentage = $totalKomentar > 0 ? round(($totalSpam / $totalKomentar) * 100, 1) : 0;

        return [
            Stat::make('Total Komentar', $totalKomentar)
                ->description('Total komentar Instagram yang terimpor.')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),
                
            Stat::make('Total Konten', $totalKonten)
                ->description('Jumlah post Instagram.')
                ->descriptionIcon('heroicon-m-photo')
                ->color('success'),
                
            Stat::make('Spam Terdeteksi', $totalSpam)
                ->description("{$spamPercentage}% dari total komentar")
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color($spamPercentage > 20 ? 'danger' : 'warning'),
        ];
    }
}