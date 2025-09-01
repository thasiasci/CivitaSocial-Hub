<?php

namespace App\Filament\Resources\KomentarUtamas\Tables;

use App\Models\KomentarUtama;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Form; 
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class KomentarUtamasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('opdChannel.opd_name')
                    ->label('Nama OPD')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Judul Video')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('textOriginal')
                    ->label('Komentar')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('authorDisplayName')
                    ->label('Username Penulis')
                    ->searchable(),
                TextColumn::make('likeCount')
                    ->label('Jumlah Like')
                    ->numeric()
                    ->sortable(),
                 TextColumn::make('totalReplyCount')
                    ->label('Jumlah Balasan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('publishedAt')
                    ->label('Tanggal Komentar')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('sentimen') 
                    ->label('Sentimen')
                    ->sortable()
                    ->badge(),
               
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make('labelSentimen')
                        ->label('Beri Label Sentimen')
                        ->modalHeading('Beri Label Sentimen')
                        ->modalSubmitActionLabel('Simpan')
                        ->modalCancelActionLabel('Batal')
                        ->form([
                            Textarea::make('textOriginal')
                                ->label('Isi Komentar Utama')
                                ->disabled()
                                ->columnSpanFull(),
                            Select::make('sentimen')
                                ->options([
                                    'Positif' => 'Positif',
                                    'Negatif' => 'Negatif',
                                    'Netral' => 'Netral',
                                ])
                                ->label('Sentimen')
                                ->required()
                                ->columnSpanFull(),
                        ])
                        ->action(function (array $data, KomentarUtama $record): void {
                            $record->sentimen = $data['sentimen'];
                            $record->save();
                            
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Label sentimen berhasil disimpan!')
                                ->success()
                                ->send();
                        }),
                ])
                ->label('Aksi')
                ->size('sm')
                ->color('gray')
                ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                Action::make('import')
                    ->label('Impor Komentar')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        FileUpload::make('file')
                            ->label('File CSV')
                            ->acceptedFileTypes(['text/csv', '.csv'])
                            ->directory('imports')
                            ->disk('public')
                            ->required()
                            ->helperText('Upload file CSV dengan format yang benar.Kolom textCleaned atau textDisplay opsional (jika tidak ada akan menggunakan textOriginal)')
                    ])
                    ->modalHeading('Impor Data Komentar Utama')
                    ->modalSubmitActionLabel('Impor')
                    ->modalCancelActionLabel('Batal')
                    ->action(function (array $data): void {
                        try {
                            $filePath = Storage::disk('public')->path($data['file']);
                            
                            if (!file_exists($filePath)) {
                                throw new \Exception('File tidak ditemukan');
                            }

                            $csv = Reader::createFromPath($filePath, 'r');
                            $csv->setHeaderOffset(0);

                            $imported = 0;
                            $errors = [];

                            foreach ($csv->getRecords() as $index => $record) {
                                try {
                                    // Import data komentar utama 
                                    if (!isset($record['parent_comment_id']) || empty($record['parent_comment_id'])) {
                                        
                                        // Tentukan textCleaned berdasarkan data yang tersedia
                                        $textCleaned = null;
                                        if (!empty($record['textCleaned'])) {
                                        
                                            $textCleaned = $record['textCleaned'];
                                        } elseif (!empty($record['textDisplay'])) {
                                            // Jika ada textDisplay tapi tidak ada textCleaned, gunakan textDisplay
                                            $textCleaned = $record['textDisplay'];
                                        } elseif (!empty($record['textOriginal'])) {
                                            // gunakan sebagai fallback
                                            $textCleaned = $record['textOriginal'];
                                        }
                                        
                                        // Konversi format datetime dari ISO 8601 ke MySQL datetime
                                        $publishedAt = null;
                                        if (!empty($record['publishedAt'])) {
                                            try {
            
                                                $publishedAt = \Carbon\Carbon::parse($record['publishedAt'])->format('Y-m-d H:i:s');
                                            } catch (\Exception $e) {
                                                
                                                $publishedAt = null;
                                            }
                                        }
                                        
                                        KomentarUtama::updateOrCreate(
                                            ['id' => $record['id'] ?? null],
                                            [
                                                'channelId' => $record['channelId'] ?? null,
                                                'videoId' => $record['videoId'] ?? null,
                                                'title' => $record['title'] ?? null,
                                                'textOriginal' => $record['textOriginal'] ?? null,
                                                'textCleaned' => $textCleaned,
                                                'authorDisplayName' => $record['authorDisplayName'] ?? null,
                                                'authorProfileImageUrl' => $record['authorProfileImageUrl'] ?? null,
                                                'authorChannelUrl' => $record['authorChannelUrl'] ?? null,
                                                'likeCount' => intval($record['likeCount'] ?? 0),
                                                'totalReplyCount' => intval($record['totalReplyCount'] ?? 0),
                                                'publishedAt' => $publishedAt,
                                            ]
                                        );
                                        $imported++;
                                    }
                                } catch (\Exception $e) {
                                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                                }
                            }

                            // Hapus file setelah diproses
                            Storage::disk('public')->delete($data['file']);

                            if ($imported > 0) {
                                Notification::make()
                                    ->title('Impor Berhasil')
                                    ->body("$imported komentar utama berhasil diimpor!" . 
                                          (!empty($errors) ? " (" . count($errors) . " baris gagal)" : ""))
                                    ->success()
                                    ->send();
                            }

                            if (!empty($errors)) {
                                Notification::make()
                                    ->title('Ada Kesalahan')
                                    ->body('Beberapa baris gagal diimpor: ' . implode(', ', array_slice($errors, 0, 3)) . 
                                          (count($errors) > 3 ? '...' : ''))
                                    ->warning()
                                    ->send();
                            }

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Impor Gagal')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
            ]);
    }
}