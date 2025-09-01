<?php

namespace App\Filament\Resources\KomentarBalasans\Tables;

use App\Models\KomentarBalasan;
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
use Carbon\Carbon;

class KomentarBalasansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('opdChannel.opd_name')
                    ->label('Nama Channel')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Judul Video')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('parentComment.textOriginal')
                    ->label('Komentar')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('textOriginal')
                    ->label('Komentar Balasan')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('authorDisplayName')
                    ->label('Username Penulis')
                    ->searchable(),
                TextColumn::make('likeCount')
                    ->label('Jumlah Suka')
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
                            ->label('Isi Komentar Balasan')
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
                    ->action(function (array $data, KomentarBalasan $record): void {
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
                            ->helperText('Upload file CSV dengan format yang benar. ')
                    ])
                    ->modalHeading('Impor Data Komentar Balasan')
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
                                    // Hanya impor data yang memiliki parent_comment_id 
                                    if (isset($record['parent_comment_id']) && !empty($record['parent_comment_id'])) {
                                        
                                        // Tentukan textCleaned berdasarkan data yang tersedia
                                        $textCleaned = null;
                                        if (!empty($record['textCleaned'])) {
                                            $textCleaned = $record['textCleaned'];
                                        } elseif (!empty($record['textDisplay'])) {
                                            $textCleaned = $record['textDisplay'];
                                        } elseif (!empty($record['textOriginal'])) {
                                            $textCleaned = $record['textOriginal'];
                                        }

                                        // Konversi format datetime 
                                        $publishedAt = null;
                                        if (!empty($record['publishedAt'])) {
                                            try {
                                                $publishedAt = Carbon::parse($record['publishedAt'])->format('Y-m-d H:i:s');
                                            } catch (\Exception $e) {
                                                
                                                $publishedAt = null;
                                            }
                                        }

                                        KomentarBalasan::updateOrCreate(
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
                                                'parent_comment_id' => $record['parent_comment_id'],
                                                'publishedAt' => $publishedAt,
                                                'totalReplyCount' => intval($record['totalReplyCount'] ?? 0),
                                            ]
                                        );
                                        $imported++;
                                    }
                                } catch (\Exception $e) {
                                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                                }
                            }
                            
                            Storage::disk('public')->delete($data['file']);

                            if ($imported > 0) {
                                Notification::make()
                                    ->title('Impor Berhasil')
                                    ->body("$imported komentar balasan berhasil diimpor!" .
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
