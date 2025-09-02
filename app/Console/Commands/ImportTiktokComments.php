<?php

namespace App\Console\Commands;

use App\Models\TiktokComment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use League\Csv\Reader;

class ImportTiktokComments extends Command
{
    protected $signature = 'app:import-tiktok-comments {path}';
    protected $description = 'Import cleaned TikTok comments from a CSV file.';

    public function handle()
    {
        $filePath = $this->argument('path');
        if (! file_exists($filePath)) {
            $this->error('File not found: ' . $filePath);
            return;
        }

        $this->info('Starting import of TikTok comments...');
        
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $record) {
            $this->info("Processing comment for author: " . ($record['authorDisplayName'] ?? 'N/A'));

            // Membersihkan kolom likeCount
            $likeCount = $this->parseCountValue($record['likeCount'] ?? '0');

            // Membersihkan kolom publishedAt
            $publishedAt = $this->parseTiktokPublishedAt($record['publishedAt'] ?? '');

            // Membersihkan kolom replyCount
            // Mengekstrak angka dari 'Lihat X balasan' atau hanya 'X'
            $replyCount = $this->parseCountValue($record['replyCount'] ?? '0');

            // Memasukkan data yang sudah bersih ke database
            TiktokComment::create([
                'authorProfileUrl' => $record['authorProfileUrl'] ?? null,
                'authorProfileImageUrl' => $record['authorProfileImageUrl'] ?? null,
                'authorDisplayName' => $record['authorDisplayName'] ?? null,
                'publishedAt' => $publishedAt,
                'comment' => $record['comment'] ?? null,
                'sentimen' => null, // Default null, bisa diisi nanti
                'likeCount' => $likeCount,
                'replyCount' => $replyCount,
            ]);
        }

        $this->info('Import completed successfully!');
    }

    /**
     * Mengonversi nilai seperti '43.6K' atau 'Lihat 59 balasan' menjadi integer.
     */
    private function parseCountValue(string $value): int
    {
        $value = strtolower(trim($value));

        // Hapus teks 'Lihat X balasan' dan ambil angkanya
        if (str_contains($value, 'lihat') && str_contains($value, 'balasan')) {
            preg_match('/(\d+)/', $value, $matches);
            return (int) ($matches[1] ?? 0);
        }
        
        // Hapus spasi dan titik sebagai pemisah ribuan
        $value = str_replace([' ', '.'], '', $value);

        // Konversi 'K' (ribuan)
        if (str_contains($value, 'k')) {
            $value = (float) str_replace('k', '', $value) * 1000;
        } 
        // Konversi 'M' (jutaan) - jika ada di data TikTok
        elseif (str_contains($value, 'm')) {
            $value = (float) str_replace('m', '', $value) * 1000000;
        }

        return (int) $value;
    }


    /**
     * Mengonversi format waktu TikTok seperti "10 j yang lalu" atau "12 m yang" menjadi Carbon instance.
     */
    private function parseTiktokPublishedAt(string $publishedAtRaw): ?Carbon
    {
        $publishedAtRaw = strtolower(trim($publishedAtRaw));
        if (empty($publishedAtRaw)) {
            return null;
        }
    
        // Ekstrak angka dari string
        preg_match('/(\d+)/', $publishedAtRaw, $matches);
        $value = (int) ($matches[1] ?? 0);
        
        if ($value === 0) { // Jika tidak ada angka yang ditemukan, tidak bisa di-parse
            return null;
        }

        if (str_contains($publishedAtRaw, 'm')) { // menit
            return Carbon::now()->subMinutes($value);
        } elseif (str_contains($publishedAtRaw, 'j')) { // jam
            return Carbon::now()->subHours($value);
        } elseif (str_contains($publishedAtRaw, 'h')) { // hari (jika ada 'h' sebagai singkatan hari)
            return Carbon::now()->subDays($value);
        } elseif (str_contains($publishedAtRaw, 'w')) { // minggu (jika ada 'w' sebagai singkatan minggu)
            return Carbon::now()->subWeeks($value);
        } elseif (str_contains($publishedAtRaw, 'bln') || str_contains($publishedAtRaw, 'bulan')) { // bulan
            return Carbon::now()->subMonths($value);
        }
    
        return null;
    }
}