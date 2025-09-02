<?php

namespace App\Filament\Resources\InstagramComments\Tables;
use App\Models\InstagramComment;
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

class InstagramCommentsTable
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
                    ->action(function (array $data, InstagramComment $record): void {
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
                                    $likeCount = (int) str_replace([' suka', '.'], '', $record['likeCount']);
                                    
                                     $publishedAt = null;
                                    $publishedAtRaw = trim($record['publishedAt'] ?? '');
                                    if (!empty($publishedAtRaw)) {
                                        $value = (int) filter_var($publishedAtRaw, FILTER_SANITIZE_NUMBER_INT);
                                        if (str_contains($publishedAtRaw, 'menit')) {
                                            $publishedAt = Carbon::now()->subMinutes($value);
                                        } elseif (str_contains($publishedAtRaw, 'jam')) {
                                            $publishedAt = Carbon::now()->subHours($value);
                                        } elseif (str_contains($publishedAtRaw, 'hari')) {
                                            $publishedAt = Carbon::now()->subDays($value);
                                        } elseif (str_contains($publishedAtRaw, 'minggu')) {
                                            $publishedAt = Carbon::now()->subWeeks($value);
                                        } elseif (str_contains($publishedAtRaw, 'bulan')) {
                                            $publishedAt = Carbon::now()->subMonths($value);
                                        }
                                    }
                                    $replyCount = (int) filter_var($record['replyCount'] ?? '', FILTER_SANITIZE_NUMBER_INT);
                                    if ($replyCount === 0 && str_contains($record['replyCount'] ?? '', 'Balas')) {
                                        $replyCount = 1;
                                    }

                                    InstagramComment::create([
                                        'authorProfileUrl' => $record['authorProfileUrl'] ?? null,
                                        'authorProfileImageUrl' => $record['authorProfileImageUrl'] ?? null,
                                        'authorDisplayName' => $record['authorDisplayName'] ?? null,
                                        'commentUrl' => $record['commentUrl'] ?? null,
                                        'publishedAt' => $publishedAt,
                                        'comment' => $record['comment'] ?? null,
                                        'sentimen' => null, // Kolom sentimen dikosongkan saat impor
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
