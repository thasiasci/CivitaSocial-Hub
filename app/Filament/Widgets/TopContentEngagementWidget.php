<?php

namespace App\Filament\Widgets;

use App\Models\InstagramComment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TopContentEngagementWidget extends BaseWidget
{
    protected static ?string $heading = 'Konten dengan Engagement Tertinggi';
    //protected int | string | array $columnSpan = 'full';
    
    public function getTableData(): Collection
    {
        $filterAkunKolaborasi = $this->table->getLivewire()->getTableFilterState('akun_kolaborasi')['value'] ?? null;
        $filterBulan = $this->table->getLivewire()->getTableFilterState('bulan')['value'] ?? null;
        
        $query = InstagramComment::query()
            ->whereNotNull('link_konten')
            ->where('link_konten', '!=', '')
            ->whereNotNull('bulan')
            ->where('bulan', '!=', '');
            

        
        if ($filterBulan) {
            $query->where('bulan', $filterBulan);
        }

        
        if ($filterAkunKolaborasi) {
            $query->whereNotNull('akun_kolaborasi')
                  ->where('akun_kolaborasi', '!=', '')
                  ->where('akun_kolaborasi', $filterAkunKolaborasi)
                  ->select([
                      'link_konten',
                      'akun_kolaborasi',
                      'bulan',
                      DB::raw('COUNT(*) as total_comments'),
                      DB::raw('COUNT(DISTINCT id_instagram) as unique_commenters'),
                      DB::raw('SUM(CASE WHEN sentimen = "Positif" THEN 1 ELSE 0 END) as positive_count'),
                      DB::raw('SUM(CASE WHEN is_spam = 1 OR is_spam = "1" OR is_spam = "true" THEN 1 ELSE 0 END) as spam_count'),
                      DB::raw('ROUND((COUNT(*) * 1.0 / NULLIF(COUNT(DISTINCT id_instagram), 0)), 2) as engagement_rate'),
                      DB::raw('ROUND((SUM(CASE WHEN sentimen = "Positif" THEN 1 ELSE 0 END) * 100.0 / COUNT(*)), 1) as positive_percentage')
                  ])
                  ->groupBy('link_konten', 'akun_kolaborasi', 'bulan');
        } else {
            
            $query->select([
                'link_konten',
                DB::raw('COALESCE(NULLIF(akun_kolaborasi, ""), "Tidak Ada Kolaborasi") as akun_kolaborasi'),
                'bulan',
                DB::raw('COUNT(*) as total_comments'),
                DB::raw('COUNT(DISTINCT id_instagram) as unique_commenters'),
                DB::raw('SUM(CASE WHEN sentimen = "Positif" THEN 1 ELSE 0 END) as positive_count'),
                DB::raw('SUM(CASE WHEN is_spam = 1 OR is_spam = "1" OR is_spam = "true" THEN 1 ELSE 0 END) as spam_count'),
                DB::raw('ROUND((COUNT(*) * 1.0 / NULLIF(COUNT(DISTINCT id_instagram), 0)), 2) as engagement_rate'),
                DB::raw('ROUND((SUM(CASE WHEN sentimen = "Positif" THEN 1 ELSE 0 END) * 100.0 / COUNT(*)), 1) as positive_percentage')
            ])
            ->groupBy('link_konten', 'bulan', 'akun_kolaborasi');
        }

        $query->orderBy('total_comments', 'desc')
              ->take(10);

        return $query->get()->map(function ($item, $index) {
            $item->id = $index + 1; 
            $item->rank = $index + 1;
            return $item;
        });
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (Builder $query) { 
                $query->whereRaw('1 = 0'); 
            })
            ->columns([
                TextColumn::make('rank')
                    ->label('#')
                    ->state(function ($record) {
                        return match($record->rank) {
                            1 => '🥇 1',
                            2 => '🥈 2', 
                            3 => '🥉 3',
                            default => $record->rank
                        };
                    })
                    ->sortable(false)
                    ->alignCenter(),
                    
                TextColumn::make('link_konten')
                    ->label('Link Konten')
                    ->searchable()
                    ->url(fn ($record) => $record->link_konten)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-link')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->link_konten;
                    }),
                    
                TextColumn::make('akun_kolaborasi')
                    ->label('Akun Kolaborasi')
                    ->searchable()
                    ->badge()
                    ->color(function ($state) {
                        return $state === 'Tidak Ada Kolaborasi' ? 'gray' : 'info';
                    })
                    ->state(function ($record) {
                        return $record->akun_kolaborasi; 
                    }),
                    
                TextColumn::make('bulan')
                    ->label('Bulan')
                    ->badge()
                    ->color('secondary'),
                    
                TextColumn::make('total_comments')
                    ->label('Total Komentar')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(function ($state) {
                        return number_format($state) . ' komentar';
                    }),

                    
                TextColumn::make('positive_percentage')
                    ->label('Sentimen Positif')
                    ->alignCenter()
                    ->badge()
                    ->color(function ($state) {
                        return match (true) {
                            $state >= 70 => 'success',
                            $state >= 50 => 'warning',
                            default => 'danger'
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format($state, 1) . '%';
                    }),
                    
                TextColumn::make('negative_percentage')
                    ->label('Sentimen Negatif')
                    ->alignCenter()
                    ->badge()
                    ->color(function ($state) {
                        return match (true) {
                            $state <= 10 => 'success',
                            $state <= 25 => 'warning',
                            default => 'danger'
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format($state, 1) . '%';
                    }),
            ])
            ->filters([
                SelectFilter::make('bulan')
                    ->label('Filter Bulan')
                    ->placeholder('Semua Bulan')
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
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query; // Query handling 
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['value']) {
                            return null;
                        }
                        return 'Bulan: ' . $data['value'];
                    }),
                    
                SelectFilter::make('akun_kolaborasi')
                    ->label('Filter Akun Kolaborasi')
                    ->options(function () {
                        return InstagramComment::distinct('akun_kolaborasi')
                            ->whereNotNull('akun_kolaborasi')
                            ->where('akun_kolaborasi', '!=', '')
                            ->orderBy('akun_kolaborasi')
                            ->pluck('akun_kolaborasi', 'akun_kolaborasi')
                            ->toArray();
                    })
                    ->placeholder('Semua Akun (Termasuk Tanpa Kolaborasi)')
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query; // Query handling ada di getTableData()
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['value']) {
                            return null;
                        }
                        return 'Akun Kolaborasi: ' . $data['value'];
                    }),
            ])
            ->defaultSort('total_comments', 'desc')
            ->striped()
            ->paginated(false)
            ->emptyStateHeading('Tidak ada data konten')
            ->emptyStateDescription('Belum ada data konten untuk filter yang dipilih. Coba ubah atau reset filter.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->heading(function () {
                $filterBulan = $this->table->getLivewire()->getTableFilterState('bulan')['value'] ?? null;
                $filterAkun = $this->table->getLivewire()->getTableFilterState('akun_kolaborasi')['value'] ?? null;
                
                $title = 'Konten dengan Engagement Tertinggi';
                
                if ($filterBulan && $filterAkun) {
                    $title .= " - {$filterBulan} ({$filterAkun})";
                } elseif ($filterBulan) {
                    $title .= " - {$filterBulan}";
                } elseif ($filterAkun) {
                    $title .= " - {$filterAkun}";
                } else {
                    $title .= " - Data Global (Semua Bulan & Akun)";
                }
                
                return $title;
            }); 
    }
    
    public function getTableRecords(): Collection
    {
        return $this->getTableData();
    }
}