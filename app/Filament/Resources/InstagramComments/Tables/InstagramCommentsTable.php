<?php

namespace App\Filament\Resources\InstagramComments\Tables;
use App\Models\InstagramComment;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Checkbox;
use League\Csv\Reader;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Form; 
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Carbon\Carbon;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class InstagramCommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('akun_kolaborasi')
                    ->label('Akun Kolaborasi')
                    ->searchable(),
                TextColumn::make('bulan') 
                    ->label('Bulan')
                    ->searchable(), 
                TextColumn::make('periode')
                    ->label('Periode')
                    ->searchable(),
                TextColumn::make('link_konten')
                    ->label('Url Konten')
                    ->searchable(),
                TextColumn::make('comment')
                    ->label('Komentar')
                    ->limit(100)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('id_instagram')
                    ->label('Username Penulis')
                    ->searchable(),
                  
                TextColumn::make('sentimen') 
                    ->label('Sentimen')
                    ->sortable()
                    ->badge(), 
                TextColumn::make('is_spam')
                    ->label('Spam')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                         if (is_null($state)) {
                             return '-'; 
                        }
                        return $state == 1 ? 'Spam' : 'Bukan Spam';
                    })
                    ->colors([
                             'danger' => fn ($state) => $state == 1,
                            'success' => fn ($state) => $state == 0,
                    ]),

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
                SelectFilter::make('bulan')
                    ->label('Filter Bulan')
                    ->placeholder('Pilih Bulan')
                    ->options([
                        'Januari' => 'Januari',
                        'Februari' => 'Februari', 
                        'Maret' => 'Maret',
                        'April' => 'April',
                        'Mei' => 'Mei',
                        'Juni' => 'Juni',
                        'Juli' => 'Juli',
                        'Agustus' => 'Agustus',
                        'September' => 'September',
                        'Oktober' => 'Oktober',
                        'November' => 'November',
                        'Desember' => 'Desember',
                    ])
                
                    ->searchable()
                    ->default(function () {
                        // Ambil bulan terbaru dari database
                        return InstagramComment::whereNotNull('bulan')
                            ->where('bulan', '!=', '')
                            ->orderByRaw("
                                CASE bulan
                                    WHEN 'Januari' THEN 1
                                    WHEN 'Februari' THEN 2
                                    WHEN 'Maret' THEN 3
                                    WHEN 'April' THEN 4
                                    WHEN 'Mei' THEN 5
                                    WHEN 'Juni' THEN 6
                                    WHEN 'Juli' THEN 7
                                    WHEN 'Agustus' THEN 8
                                    WHEN 'September' THEN 9
                                    WHEN 'Oktober' THEN 10
                                    WHEN 'November' THEN 11
                                    WHEN 'Desember' THEN 12
                                END DESC
                            ")
                            ->value('bulan');
                    }),
                SelectFilter::make('sentimen')
                    ->label('Filter Sentimen')
                    ->placeholder('Pilih Sentimen')
                    ->options([
                        'Positif' => 'Positif',
                        'Netral' => 'Netral',
                        'Negatif' => 'Negatif',
                    ]),
                SelectFilter::make('is_spam')
                    ->label('Filter Status Spam')
                    ->placeholder('Pilih Status')
                    ->options([
                        0 => 'Bukan Spam',
                        1 => 'Spam',
                    ]),
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
                    EditAction::make('labelSpam')
                    ->label('Beri Label Spam')
                    ->modalHeading('Beri Label Spam')
                    ->modalSubmitActionLabel('Simpan')
                    ->modalCancelActionLabel('Batal')
                    ->form([
                        Textarea::make('comment')
                                ->label('Isi Komentar')
                                ->disabled()
                                ->columnSpanFull(),
                            Select::make('is_spam')
                                ->options([
                                    0 => 'Bukan Spam',
                                    1 => 'Spam',
                                ])
                                ->label('Status Spam')
                                ->required()
                                ->columnSpanFull(),
                    ])
                    ->action(function (array $data, InstagramComment $record): void {
                        $record->is_spam = $data['is_spam'];
                        $record->save();
                        
                       
                    }),
                ])
                ->label('Aksi')
                ->size('sm')
                ->color('gray')
                ->button(),
            ])
            ->toolbarActions([
                Action::make('import')
                    ->label('Import Data')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        FileUpload::make('file')
                            ->label('File CSV')
                            ->acceptedFileTypes(['text/csv', '.csv'])
                            ->directory('imports')
                            ->disk('public')
                            ->required()
                            ->helperText('Upload file CSV dengan kolom: akun_kolaborasi, link_konten, periode, bulan, comment, id_instagram')
                    ])
                    ->modalHeading('Import Data Instagram Comments')
                    ->modalSubmitActionLabel('Import')
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
                            $skipped = 0;
                            $errors = [];

                            foreach ($csv->getRecords() as $index => $record) {
                                try {
                                    // Mapping kolom untuk berbagai variasi nama
                                    $mappedRecord = [
                                        'akun_kolaborasi' => $record['Akun kolaborasi'] ?? $record['akun_kolaborasi'] ?? null,
                                        'link_konten' => $record['Link Konten'] ?? $record['link_konten'] ?? null,
                                        'id_instagram' => $record['ID Instagram'] ?? $record['id_instagram'] ?? null,
                                        'periode' => $record['PERIODE'] ?? $record['periode'] ?? null,
                                        'comment' => $record['Comment'] ?? $record['comment'] ?? null,
                                        'bulan' => $record['Bulan'] ?? $record['bulan'] ?? null,
                                        'sentimen' => $record['sentimen'] ?? null,
                                        'is_spam' => $record['is_spam'] ?? null,
                                    ];

                                    // Validasi field wajib
                                    $requiredFields = ['akun_kolaborasi', 'link_konten', 'periode', 'bulan', 'comment'];
                                    foreach ($requiredFields as $field) {
                                        if (empty($mappedRecord[$field])) {
                                            throw new \Exception("Field {$field} tidak boleh kosong");
                                        }
                                    }

                                   
                                    $exists = InstagramComment::where('link_konten', $mappedRecord['link_konten'])
                                        ->where('comment', $mappedRecord['comment'])
                                        ->exists();
                                    
                                    if ($exists) {
                                        $skipped++;
                                        continue;
                                    }

                                    
                                    InstagramComment::create([
                                        'akun_kolaborasi' => (string)$mappedRecord['akun_kolaborasi'],
                                        'link_konten' => (string)$mappedRecord['link_konten'],
                                        'periode' => (string)$mappedRecord['periode'],
                                        'bulan' => (string)$mappedRecord['bulan'],
                                        'comment' => (string)$mappedRecord['comment'],
                                        'id_instagram' => $mappedRecord['id_instagram'],
                                        'sentimen' => $mappedRecord['sentimen'],
                                        'is_spam' => isset($mappedRecord['is_spam']) ? (int)$mappedRecord['is_spam'] : null,
                                    ]);

                                    $imported++;

                                } catch (\Exception $e) {
                                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                                }
                            }

                            
                            Storage::disk('public')->delete($data['file']);

                            if ($imported > 0) {
                                Notification::make()
                                    ->title('Import Berhasil')
                                    ->body("$imported data berhasil diimport!" . 
                                          ($skipped > 0 ? " ($skipped data duplikat dilewati)" : "") .
                                          (!empty($errors) ? " (" . count($errors) . " baris gagal)" : ""))
                                    ->success()
                                    ->send();
                            }

                            if (!empty($errors)) {
                                Notification::make()
                                    ->title('Ada Kesalahan')
                                    ->body('Beberapa baris gagal diimport: ' . implode(', ', array_slice($errors, 0, 3)) . 
                                          (count($errors) > 3 ? '...' : ''))
                                    ->warning()
                                    ->send();
                            }

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Import Gagal')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                    
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                           
            ]);
    }

    private static function processRecord(array $record, array $options, int $rowNumber): array
    {
        try {
            // Mapping kolom yang mungkin berbeda
            $mappedRecord = self::mapColumns($record);
            
            // field wajib
            $requiredFields = ['akun_kolaborasi', 'link_konten', 'periode', 'bulan', 'comment'];
            
            if ($options['validate_required']) {
                foreach ($requiredFields as $field) {
                    if (empty($mappedRecord[$field])) {
                        return [
                            'status' => 'error',
                            'message' => "Baris {$rowNumber}: Field {$field} tidak boleh kosong"
                        ];
                    }
                }
            }

           
            if ($options['skip_duplicates']) {
                $exists = InstagramComment::where('link_konten', $mappedRecord['link_konten'])
                    ->where('comment', $mappedRecord['comment'])
                    ->exists();
                
                if ($exists) {
                    return [
                        'status' => 'skipped',
                        'message' => "Baris {$rowNumber}: Data sudah ada (duplikat)"
                    ];
                }
            }

            // Insert data
            InstagramComment::create([
                'akun_kolaborasi' => $mappedRecord['akun_kolaborasi'],
                'link_konten' => $mappedRecord['link_konten'],
                'periode' => $mappedRecord['periode'],
                'bulan' => $mappedRecord['bulan'],
                'comment' => $mappedRecord['comment'],
                'id_instagram' => $mappedRecord['id_instagram'] ?? null,
                'sentimen' => $mappedRecord['sentimen'] ?? null,
                'is_spam' => isset($mappedRecord['is_spam']) ? (int)$mappedRecord['is_spam'] : null,
            ]);

            return [
                'status' => 'imported',
                'message' => "Baris {$rowNumber}: Berhasil diimport"
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error', 
                'message' => "Baris {$rowNumber}: Error - " . $e->getMessage()
            ];
        }
    }

    private static function mapColumns(array $record): array
    {
        // Mapping untuk berbagai variasi nama kolom
        $columnMappings = [
            'akun_kolaborasi' => ['akun_kolaborasi', 'akun kolaborasi', 'collaboration_account', 'akun'],
            'link_konten' => ['link_konten', 'link konten', 'url', 'link', 'content_link'],
            'periode' => ['periode', 'period', 'tanggal', 'date'],
            'bulan' => ['bulan', 'month'],
            'comment' => ['comment', 'komentar', 'comments', 'isi_comment'],
            'id_instagram' => ['id_instagram', 'username', 'user', 'instagram_id', 'penulis'],
            'sentimen' => ['sentimen', 'sentiment'],
            'is_spam' => ['is_spam', 'spam', 'status_spam']
        ];

        $mappedRecord = [];

        foreach ($columnMappings as $targetColumn => $possibleNames) {
            $mappedRecord[$targetColumn] = null;
            
            foreach ($possibleNames as $possibleName) {
                if (array_key_exists($possibleName, $record) && !empty($record[$possibleName])) {
                    $mappedRecord[$targetColumn] = trim($record[$possibleName]);
                    break;
                }
            }
        }

        return $mappedRecord;
    }
}