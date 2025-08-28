<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\KamusSingkatan;
use App\Models\KamusEjaan;
use App\Models\KomentarUtama;
use App\Models\KomentarBalasan;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class KamusAnalisaWidget extends BaseWidget
{
    use InteractsWithPageFilters;
    
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $singkatanStats = $this->getSingkatanAnalysis();
        $ejaanStats = $this->getEjaanAnalysis();
        $penggunaanStats = $this->getPenggunaanAnalysis();

        return [
            // Stat 1: Total Kamus
            Stat::make('Total Kamus', number_format($singkatanStats['total'] + $ejaanStats['total']))
                ->description("Singkatan: {$singkatanStats['total']} | Ejaan: {$ejaanStats['total']}")
                ->descriptionIcon('heroicon-o-book-open'),

            // Stat 2: Singkatan Terpopuler
            Stat::make('Singkatan Terpopuler', new HtmlString($this->formatTopSingkatan($singkatanStats['top'])))
                ->description("Berdasarkan penggunaan dalam komentar")
                ->descriptionIcon('heroicon-o-hashtag'),

 
            Stat::make('Kesalahan Ejaan Terbanyak', new HtmlString($this->formatTopEjaan($ejaanStats['top'])))
                ->description("Yang paling sering ditemukan dalam komentar")
                ->descriptionIcon('heroicon-o-exclamation-triangle'),
        ];
    }

    private function getSingkatanAnalysis(): array
    {
        $totalSingkatan = KamusSingkatan::count();
        
        // Ambil singkatan dan hitung penggunaan dalam komentar utama dan balasan
        $singkatanList = KamusSingkatan::all();
        $singkatanUsage = [];

        foreach ($singkatanList as $singkatan) {
            // Hitung di komentar utama - cari di textOriginal dan textCleaned
            $countUtama = KomentarUtama::where(function($query) use ($singkatan) {
                $query->where('textOriginal', 'LIKE', '%' . $singkatan->singkatan . '%')
                      ->orWhere('textCleaned', 'LIKE', '%' . $singkatan->singkatan . '%');
            })->count();
            
            // Hitung di komentar balasan - cari di textOriginal dan textCleaned
            $countBalasan = KomentarBalasan::where(function($query) use ($singkatan) {
                $query->where('textOriginal', 'LIKE', '%' . $singkatan->singkatan . '%')
                      ->orWhere('textCleaned', 'LIKE', '%' . $singkatan->singkatan . '%');
            })->count();
            
            $totalCount = $countUtama + $countBalasan;
            
            if ($totalCount > 0) {
                $singkatanUsage[] = [
                    'singkatan' => $singkatan->singkatan,
                    'kepanjangan' => $singkatan->kepanjangan,
                    'usage_count' => $totalCount
                ];
            }
        }

        // Sort berdasarkan usage
        usort($singkatanUsage, function($a, $b) {
            return $b['usage_count'] - $a['usage_count'];
        });

        return [
            'total' => $totalSingkatan,
            'top' => array_slice($singkatanUsage, 0, 5)
        ];
    }

    private function getEjaanAnalysis(): array
    {
        $totalEjaan = KamusEjaan::count();
        
        // Ambil ejaan salah dan hitung dalam komentar utama dan balasan
        $ejaanList = KamusEjaan::all();
        $ejaanUsage = [];

        foreach ($ejaanList as $ejaan) {
            // Hitung di komentar utama - cari di textOriginal dan textCleaned
            $countUtama = KomentarUtama::where(function($query) use ($ejaan) {
                $query->where('textOriginal', 'LIKE', '%' . $ejaan->ejaan_salah . '%')
                      ->orWhere('textCleaned', 'LIKE', '%' . $ejaan->ejaan_salah . '%');
            })->count();
            
            // Hitung di komentar balasan - cari di textOriginal dan textCleaned
            $countBalasan = KomentarBalasan::where(function($query) use ($ejaan) {
                $query->where('textOriginal', 'LIKE', '%' . $ejaan->ejaan_salah . '%')
                      ->orWhere('textCleaned', 'LIKE', '%' . $ejaan->ejaan_salah . '%');
            })->count();
            
            $totalCount = $countUtama + $countBalasan;
            
            if ($totalCount > 0) {
                $ejaanUsage[] = [
                    'ejaan_salah' => $ejaan->ejaan_salah,
                    'ejaan_benar' => $ejaan->ejaan_benar,
                    'usage_count' => $totalCount
                ];
            }
        }

        // Sort berdasarkan usage
        usort($ejaanUsage, function($a, $b) {
            return $b['usage_count'] - $a['usage_count'];
        });

        return [
            'total' => $totalEjaan,
            'top' => array_slice($ejaanUsage, 0, 5)
        ];
    }

    private function getPenggunaanAnalysis(): array
    {
        // Analisa tambahan bisa dikembangkan disini
        return [];
    }

    private function formatTopSingkatan(array $topSingkatan): string
    {
        if (empty($topSingkatan)) {
            return '<span class="text-gray-600 dark:text-gray-400">Tidak ada penggunaan singkatan</span>';
        }

        $html = '<div style="font-size: 15px; line-height: 1.4;">';
        foreach ($topSingkatan as $index => $item) {
            $rank = $index + 1;
            
            $html .= "<div class='mb-1 p-1.5 border-l-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800' style='margin-bottom: 4px; padding: 3px 6px;'>";
            $html .= "<strong class='text-gray-900 dark:text-gray-100'>{$rank }  .  {$item['singkatan']}</strong> → <span class='text-gray-700 dark:text-gray-300'>{$item['kepanjangan']}</span>";
            $html .= "<div class='text-gray-600 dark:text-gray-400' style='font-size: 12px; margin-top: 2px;'>Digunakan: {$item['usage_count']}x</div>";
            $html .= "</div>";
        }
        $html .= '</div>';

        return $html;
    }

    private function formatTopEjaan(array $topEjaan): string
    {
        if (empty($topEjaan)) {
            return '<span class="text-gray-600 dark:text-gray-400">Tidak ada kesalahan ejaan ditemukan</span>';
        }

        $html = '<div style="font-size: 15px; line-height: 1.4;">';
        foreach ($topEjaan as $index => $item) {
            $rank = $index + 1;
            
            $html .= "<div class='mb-1 p-1.5 border-l-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800' style='margin-bottom: 4px; padding: 3px 6px;'>";
            $html .= "<span class='line-through text-gray-600 dark:text-gray-400'>{$item['ejaan_salah']}</span>";
            $html .= " → <strong class='text-gray-900 dark:text-gray-100'>{$item['ejaan_benar']}</strong>";
            $html .= "<div class='text-gray-600 dark:text-gray-400' style='font-size: 12px; margin-top: 2px;'>Ditemukan: {$item['usage_count']}x</div>";
            $html .= "</div>";
        }
        $html .= '</div>';

        return $html;
    }
}