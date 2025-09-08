<?php

namespace App\Filament\Resources\InstagramComments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class InstagramCommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('link_konten')
                    ->label('Link Konten')
                    ->url()
                    ->required()
                    ->default(null),
                    
                TextInput::make('periode')
                    ->label('Periode')
                    ->required()
                    ->placeholder('Contoh: 2-Mei-2025')
                    ->default(null),
                    
                Textarea::make('comment')
                    ->label('Komentar')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                    
                Select::make('bulan')
                    ->label('Bulan')
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
                    ->required()
                    ->searchable()
                    ->default(null),
            ]);
    }
}