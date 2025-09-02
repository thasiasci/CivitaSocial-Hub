<?php

namespace App\Console\Commands;

use App\Models\InstagramComment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use League\Csv\Reader;

class ImportInstagramComments extends Command
{
    protected $signature = 'app:import-instagram-comments {path}';
    protected $description = 'Import cleaned Instagram comments from a CSV file.';

    public function handle()
    {
        $filePath = $this->argument('path');
        if (! file_exists($filePath)) {
            $this->error('File not found: ' . $filePath);
            return;
        }

        $this->info('Starting import of Instagram comments...');
        
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $record) {
            $this->info("Processing comment by: " . $record['authorDisplayName']);

            // Membersihkan kolom likeCount
            $likeCount = (int) str_replace([' suka', '.'], '', $record['likeCount'] ?? '0');

            // Membersihkan kolom publishedAt
            $publishedAt = $this->parsePublishedAt($record['publishedAt'] ?? '');

            // Membersihkan kolom replyCount
            $replyCount = (int) filter_var($record['replyCount'] ?? '0', FILTER_SANITIZE_NUMBER_INT);
            if ($replyCount == 0 && str_contains($record['replyCount'] ?? '', 'Balas')) {
                $replyCount = 1;
            }

            // Memasukkan data yang sudah bersih ke database
            InstagramComment::create([
                'authorProfileUrl' => $record['authorProfileUrl'] ?? null,
                'authorProfileImageUrl' => $record['authorProfileImageUrl'] ?? null,
                'authorDisplayName' => $record['authorDisplayName'] ?? null,
                'commentUrl' => $record['commentUrl'] ?? null,
                'publishedAt' => $publishedAt,
                'comment' => $record['comment'] ?? null,
                'sentimen' => null,
                'likeCount' => $likeCount,
                'replyCount' => $replyCount,
            ]);
        }

        $this->info('Import completed successfully!');
    }

    private function parsePublishedAt(string $publishedAtRaw): ?Carbon
    {
        $publishedAtRaw = trim($publishedAtRaw);
        if (empty($publishedAtRaw)) {
            return null;
        }
    
        $value = (int) filter_var($publishedAtRaw, FILTER_SANITIZE_NUMBER_INT);
    
        if (str_contains($publishedAtRaw, 'menit')) {
            return Carbon::now()->subMinutes($value);
        } elseif (str_contains($publishedAtRaw, 'jam')) {
            return Carbon::now()->subHours($value);
        } elseif (str_contains($publishedAtRaw, 'hari')) {
            return Carbon::now()->subDays($value);
        } elseif (str_contains($publishedAtRaw, 'minggu')) {
            return Carbon::now()->subWeeks($value);
        } elseif (str_contains($publishedAtRaw, 'bulan')) {
            return Carbon::now()->subMonths($value);
        }
    
        return null;
    }
}