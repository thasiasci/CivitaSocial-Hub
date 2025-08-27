<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\KomentarUtama;
use App\Models\OpdChannel;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class TopVideosWidget extends BaseWidget
{
    use InteractsWithPageFilters;
    
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    
    // Override method untuk mengatur jumlah kolom menjadi 1
    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        $topVideos = $this->getTopVideos();
        
        if (empty($topVideos)) {
            return [
                Stat::make('TOP 5 VIDEO TERPOPULER', 'Tidak ada data')
                    ->description('Tidak ada video untuk filter yang dipilih')
                    ->descriptionIcon('heroicon-o-video-camera-slash')
                    ->color('gray')
            ];
        }

        // Format data menjadi HTML list
        $videoList = '';
        foreach ($topVideos as $index => $video) {
            $rank = $index + 1;
            
            $videoList .= "<div style='margin-bottom: 8px; padding: 6px; border-radius: 6px; border-left: 3px solid " .
                         match($rank) {
                             1 => '#f59e0b',
                             2 => '#6b7280', 
                             3 => '#f97316',
                             default => '#3b82f6'
                         } . ";'>";
            
            $videoList .= "<div style='margin-bottom: 4px;'>";
            $videoList .= "<strong style='font-size: 20px; line-height: 1.4; word-wrap: break-word; display: block;'>{$rank}. " . htmlspecialchars($video['title']) . "</strong>";
            $videoList .= "</div>";
            
            $videoList .= "<div style='font-size: 15px; margin-left: 16px;'>";
            $videoList .= "<span style='margin-right: 12px;'>Channel: " . htmlspecialchars($video['channel_name']) . "</span>";
            $videoList .= "<span style='margin-right: 12px;'>Komentar: " . number_format($video['total_komentar_utama']) . "</span>";
            $videoList .= "</div>";
            
            if ($video['youtube_url']) {
                $videoList .= "<div style='margin-left: 16px; margin-top: 4px;'>";
                $videoList .= "<a href='" . htmlspecialchars($video['youtube_url']) . "' target='_blank' style='font-size: 10px; text-decoration: none; background: #fb0000b1; padding: 2px 6px; border-radius: 4px; color: white;'>YouTube</a>";
                $videoList .= "</div>";
            }
            
            $videoList .= "</div>";
        }

        $selectedChannelName = $this->getSelectedChannelName();
        $title = 'TOP 5 VIDEO TERPOPULER' . ($selectedChannelName !== 'Semua Channel' ? " - {$selectedChannelName}" : '');

        return [
            Stat::make($title, new HtmlString($videoList))
                ->description(" " . count($topVideos) . " video terpopuler ")
                ->descriptionIcon('heroicon-o-fire')
                ->color('warning')
        ];
    }

    private function getTopVideos(): array
    {
        // Ambil filter dari dashboard
        $channelId = $this->filters['channelId'] ?? null;
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $query = KomentarUtama::select([
            'videoId',
            'title',
            'channelId',
            DB::raw('COUNT(DISTINCT komentar_utama.id) as total_komentar_utama'),
            DB::raw('COALESCE(SUM(komentar_utama.totalReplyCount), 0) as total_balasan'),
            DB::raw('COUNT(DISTINCT komentar_utama.id) + COALESCE(SUM(komentar_utama.totalReplyCount), 0) as total_engagement'),
            DB::raw('AVG(komentar_utama.likeCount) as avg_likes'),
            DB::raw('MAX(komentar_utama.publishedAt) as latest_comment')
        ])
        ->groupBy('videoId', 'title', 'channelId');

        // Terapkan filter sesuai dengan yang ada di dashboard
        if ($channelId) {
            $query->where('channelId', $channelId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('publishedAt', [$startDate, $endDate]);
        }

        return $query
            ->orderBy('total_engagement', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($video) {
                // Ambil data channel
                $channel = OpdChannel::where('channel_id', $video->channelId)->first();
                
                return [
                    'video_id' => $video->videoId,
                    'title' => $this->truncateTitle($video->title, 80),
                    'full_title' => $video->title,
                    'channel_name' => $channel->opd_name ?? 'Unknown Channel',
                    'total_komentar_utama' => $video->total_komentar_utama,
                    'youtube_url' => "https://www.youtube.com/watch?v=" . $video->videoId,
                ];
            })
            ->toArray();
    }
    
    private function truncateTitle(string $title, int $length): string
    {
        return strlen($title) > $length ? substr($title, 0, $length) . '...' : $title;
    }
    
    private function getSelectedChannelName(): string
    {
        $channelId = $this->filters['channelId'] ?? null;
        if (!$channelId) {
            return 'Semua Channel';
        }
        
        $channel = OpdChannel::where('channel_id', $channelId)->first();
        return $channel ? $channel->opd_name : 'Unknown Channel';
    }
}