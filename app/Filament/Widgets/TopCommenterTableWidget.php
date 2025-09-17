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

class TopCommenterTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Pemberi Komentar Teratas';
    //protected int | string | array $columnSpan = 'full';
    
    public function getTableData(): Collection
    {
        $filterAkunKolaborasi = $this->table->getLivewire()->getTableFilterState('akun_kolaborasi')['value'] ?? null;
        $filterBulan = $this->table->getLivewire()->getTableFilterState('bulan')['value'] ?? null;
        
        $query = InstagramComment::query()
            ->whereNotNull('id_instagram')
            ->where('id_instagram', '!=', '');

        //  filter bulan
        if ($filterBulan) {
            $query->where('bulan', $filterBulan);
        }

        // filter akun kolaborasi
        if ($filterAkunKolaborasi) {
            $query->where('akun_kolaborasi', $filterAkunKolaborasi)
                  ->selectRaw('id_instagram, akun_kolaborasi, COUNT(*) as total_comments')
                  ->groupBy('id_instagram', 'akun_kolaborasi');
        } else {
            $query->selectRaw("id_instagram, 'Semua Akun' as akun_kolaborasi, COUNT(*) as total_comments")
                  ->groupBy('id_instagram');
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
                    
                TextColumn::make('id_instagram')
                    ->label('Username Instagram')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->icon('heroicon-o-user'),
                    
                TextColumn::make('akun_kolaborasi')
                    ->label('Akun Kolaborasi')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->state(function ($record) {
                        return $record->akun_kolaborasi; 
                    }),
                    
                TextColumn::make('total_comments')
                    ->label('Total Komentar')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(function ($state) {
                        return number_format($state) . ' komentar';
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
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query; // Query handling ada di getTableData()
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
                    ->placeholder('Semua Akun')
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
            ->emptyStateHeading('Tidak ada data commenter')
            ->emptyStateDescription('Belum ada data komentar untuk filter yang dipilih. Coba ubah atau reset filter.')
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right')
            ->heading(function () {
                $filterBulan = $this->table->getLivewire()->getTableFilterState('bulan')['value'] ?? null;
                $filterAkun = $this->table->getLivewire()->getTableFilterState('akun_kolaborasi')['value'] ?? null;
                
                $title = 'Pemberi Komentar Teratas';
                
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