<?php

namespace App\Filament\Resources\TiktokComments\Tables;

use App\Models\TiktokComment;
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
class TiktokCommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('authorDisplayName')
                    ->label('Username Penulis')
                    ->searchable(),
                TextColumn::make('comment')
                    ->label('Komentar')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('publishedAt')
                    ->label('Tanggal Komentar')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('likeCount')
                    ->label('Jumlah Suka')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('replyCount')
                    ->label('Jumlah Balasan')
                    ->searchable(),
                TextColumn::make('sentimen') 
                    ->label('Sentimen')
                    ->sortable()
                    ->badge(), 
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        Textarea::make('comment')
                            ->label('Isi Komentar ')
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
                    ->action(function (array $data, TiktokComment $record): void {
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
                    ->modalHeading('Impor Data Komentar Instagram')
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
                                    // Helper function untuk membersihkan nilai hitungan (like/reply)
                                    $parseCountValue = function (string $value): int {
                                        $value = strtolower(trim($value));
                                        if (str_contains($value, 'lihat') && str_contains($value, 'balasan')) {
                                            preg_match('/(\d+)/', $value, $matches);
                                            return (int) ($matches[1] ?? 0);
                                        }
                                        $value = str_replace([' ', '.'], '', $value);
                                        if (str_contains($value, 'k')) {
                                            return (int) ((float) str_replace('k', '', $value) * 1000);
                                        }
                                        if (str_contains($value, 'm')) {
                                            return (int) ((float) str_replace('m', '', $value) * 1000000);
                                        }
                                        return (int) $value;
                                    };

                                    // Helper function untuk mengonversi waktu
                                    $parseTiktokPublishedAt = function (string $publishedAtRaw): ?Carbon {
                                        $publishedAtRaw = strtolower(trim($publishedAtRaw));
                                        if (empty($publishedAtRaw)) {
                                            return null;
                                        }
                                        preg_match('/(\d+)/', $publishedAtRaw, $matches);
                                        $value = (int) ($matches[1] ?? 0);
                                        if ($value === 0) {
                                            return null;
                                        }
                                        if (str_contains($publishedAtRaw, 'm')) {
                                            return Carbon::now()->subMinutes($value);
                                        } elseif (str_contains($publishedAtRaw, 'j')) {
                                            return Carbon::now()->subHours($value);
                                        } elseif (str_contains($publishedAtRaw, 'h')) {
                                            return Carbon::now()->subDays($value);
                                        } elseif (str_contains($publishedAtRaw, 'w')) {
                                            return Carbon::now()->subWeeks($value);
                                        } elseif (str_contains($publishedAtRaw, 'bln') || str_contains($publishedAtRaw, 'bulan')) {
                                            return Carbon::now()->subMonths($value);
                                        }
                                        return null;
                                    };

                                    $likeCount = $parseCountValue($record['likeCount'] ?? '0');
                                    $replyCount = $parseCountValue($record['replyCount'] ?? '0');
                                    $publishedAt = $parseTiktokPublishedAt($record['publishedAt'] ?? '');
                                    
                                    TiktokComment::create([
                                        'authorProfileUrl' => $record['authorProfileUrl'] ?? null,
                                        'authorProfileImageUrl' => $record['authorProfileImageUrl'] ?? null,
                                        'authorDisplayName' => $record['authorDisplayName'] ?? null,
                                        'publishedAt' => $publishedAt,
                                        'comment' => $record['comment'] ?? null,
                                        'sentimen' => null,
                                        'likeCount' => $likeCount,
                                        'replyCount' => $replyCount,
                                    ]);
                                    $imported++;
                                } catch (\Exception $e) {
                                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                                }
                            }
                            
                            Storage::disk('public')->delete($data['file']);

                            if ($imported > 0) {
                                Notification::make()
                                    ->title('Impor Berhasil')
                                    ->body("$imported komentar berhasil diimpor!" .
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
                    }),
            ]);
    }
}
