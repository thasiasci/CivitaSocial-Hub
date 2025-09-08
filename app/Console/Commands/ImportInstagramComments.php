<?php

namespace App\Console\Commands;

use App\Models\InstagramComment;
use Illuminate\Console\Command;
use League\Csv\Reader;

class ImportInstagramComments extends Command
{
    protected $signature = 'app:import-instagram-comments {path}';
    protected $description = 'Import manual Instagram comments from a CSV file.';

    public function handle()
    {
        $filePath = $this->argument('path');
        if (!file_exists($filePath)) {
            $this->error('File tidak ditemukan: ' . $filePath);
            return;
        }

        $this->info('Memulai impor komentar Instagram...');

        try {
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setHeaderOffset(0);

            $importedCount = 0;
            foreach ($csv->getRecords() as $record) {
                $periode = $record['PERIODE'] ?? null;

                InstagramComment::create([
                    'link_konten' => $record['Link Konten'] ?? null,
                    'periode' => $periode, 
                    'comment' => $record['Comment'] ?? null,
                    'sentimen' => $record['Sentimen'] ?? null,
                    'bulan' => $record['Bulan'] ?? null,
                ]);

                $importedCount++;
            }

            $this->info("Impor berhasil! Total {$importedCount} komentar dimasukkan.");
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan saat mengimpor: ' . $e->getMessage());
        }
    }
}