<?php

namespace App\Filament\Widgets;

use App\Models\InstagramComment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;


class InstagramOverview extends StatsOverviewWidget
{
    
    protected function getStats(): array
    {
        $totalKomentar = InstagramComment::count();
        $totalKonten = InstagramComment::distinct('link_konten')->count();
        $totalAkunKolaborasi = InstagramComment::distinct('akun_kolaborasi')->count();
        $totalSpam = InstagramComment::where('is_spam', true)->count();
        $spamPercentage = $totalKomentar > 0 ? round(($totalSpam / $totalKomentar) * 100, 1) : 0;
        $engagementRate = $totalKonten > 0 ? round($totalKomentar / $totalKonten, 1) : 0;

        $mostFrequentPoster = InstagramComment::select('akun_kolaborasi', DB::raw('COUNT(DISTINCT link_konten) as total_posts'))
            ->whereNotNull('akun_kolaborasi')
            ->where('akun_kolaborasi', '!=', '')
            ->whereNotNull('link_konten')
            ->where('link_konten', '!=', '')
            ->groupBy('akun_kolaborasi')
            ->orderByDesc('total_posts')
            ->first();

        $mostFrequentPosterName = $mostFrequentPoster->akun_kolaborasi ?? 'N/A';
        $mostFrequentPosterPosts = $mostFrequentPoster->total_posts ?? 0;

        return [
            Stat::make('Total Komentar', number_format($totalKomentar))
                ->description('Total komentar Instagram yang terimpor.')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),
                
            Stat::make('Total Konten',number_format ($totalKonten))
                ->description('Jumlah post Instagram.')
                ->descriptionIcon('heroicon-m-photo')
                ->color('success'),
                
            Stat::make('Total Akun Kolaborasi', $totalAkunKolaborasi)
                ->description('Jumlah akun yang berkolaborasi.')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
                
            Stat::make('Engagement Rate', $engagementRate)
                ->description('Rata-rata komentar per konten')
                ->descriptionIcon('heroicon-m-heart')
                ->color('purple'),
                
            Stat::make('Spam Terdeteksi', $totalSpam)
                ->description("{$spamPercentage}% dari total komentar")
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color($spamPercentage > 20 ? 'danger' : 'warning'),

            Stat::make('Akun Kolaborasi Teraktif', $mostFrequentPosterName)
                ->description("Dengan {$mostFrequentPosterPosts} konten.")
                ->descriptionIcon('heroicon-m-sparkles') // Atau ikon lain yang relevan
                ->color('amber'),
        ];
    }
}