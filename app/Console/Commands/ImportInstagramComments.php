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
                // Skip baris kosong atau yang tidak punya data penting
                if (empty(trim($record['Link Konten'] ?? '')) && empty(trim($record['Comment'] ?? ''))) {
                    continue;
                }

                InstagramComment::create([
                    'akun_kolaborasi' => trim($record['Akun kolaborasi'] ?? '') ?: null,
                    'link_konten' => trim($record['Link Konten'] ?? '') ?: null,
                    'id_instagram' => trim($record['ID Instagram'] ?? '') ?: null,
                    'periode' => trim($record['PERIODE'] ?? '') ?: null,
                    'comment' => trim($record['Comment'] ?? '') ?: null,
                    'sentimen' => trim($record['Sentimen'] ?? '') ?: null,
                    'bulan' => trim($record['Bulan'] ?? '') ?: null,
                ]);

                $importedCount++;
            }

            $this->info("Impor berhasil! Total {$importedCount} komentar dimasukkan.");
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan saat mengimpor: ' . $e->getMessage());
        }
    }
}