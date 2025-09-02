<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\OpdChannel;
use Filament\Forms\Components\Select;
use App\Filament\Widgets\ChannelCommentStats;
use App\Filament\Widgets\GlobalStats;
use App\Filament\Widgets\CommentsPerDayChart;
use App\Filament\Widgets\TopVideosWidget;
use App\Filament\Widgets\CommentSentimentChart;
use App\Filament\Widgets\KamusAnalisaWidget;
use Filament\Forms\Components\Grid;


class Dashboard extends BaseDashboard
{
    use HasFiltersForm;
    
    public function getWidgets(): array
    {
        return [
            GlobalStats::class, // Widget global
            ChannelCommentStats::class, // Widget interaktif
            CommentsPerDayChart::class,
            CommentSentimentChart::class,
            TopVideosWidget::class,
        ];
    }

    public function filtersForm(Schema $schema): Schema
    {
        logger('filtersForm dipanggil');
        return $schema
            ->components([
                Section::make()
                    ->columns(3) // 3 kolom dalam satu baris
                    ->schema([
                        Select::make('channelId') 
                            ->label('Pilih Channel')
                            ->options(OpdChannel::whereNotNull('channel_id')
                                    ->where('channel_id', '!=', '')
                                    ->pluck('opd_name', 'channel_id'))
                            ->placeholder('Semua Channel')
                            ->live(),

                        DatePicker::make('startDate')
                            ->label('Tanggal Mulai')
                            ->live(),

                        DatePicker::make('endDate')
                            ->label('Tanggal Selesai')
                            ->live(),
                    ])
                    ->columnSpanFull(), 
            ]);
    }
    
       
}