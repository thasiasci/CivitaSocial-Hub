<?php

namespace App\Filament\Resources\KomentarUtamas;

use App\Filament\Resources\KomentarUtamas\Pages\CreateKomentarUtama;
use App\Filament\Resources\KomentarUtamas\Pages\EditKomentarUtama;
use App\Filament\Resources\KomentarUtamas\Pages\ListKomentarUtamas;
use App\Filament\Resources\KomentarUtamas\Pages\ViewKomentarUtama;
use App\Filament\Resources\KomentarUtamas\Schemas\KomentarUtamaForm;
use App\Filament\Resources\KomentarUtamas\Schemas\KomentarUtamaInfolist;
use App\Filament\Resources\KomentarUtamas\Tables\KomentarUtamasTable;
use App\Models\KomentarUtama;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KomentarUtamaResource extends Resource
{
    protected static ?string $model = KomentarUtama::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationLabel = 'Komentar Utama';
    protected static ?string $pluralModelLabel = 'Data Komentar Utama';
    protected static string | UnitEnum | null $navigationGroup = 'Komentar';
    protected static ?int $navigationSort = 1; 
    protected static ?string $navigationBadgeTooltip = 'Jumlah Komentar Utama';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Schema $schema): Schema
    {
        return KomentarUtamaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KomentarUtamaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KomentarUtamasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKomentarUtamas::route('/'),
            'create' => CreateKomentarUtama::route('/create'),
            'view' => ViewKomentarUtama::route('/{record}'),
            //'edit' => EditKomentarUtama::route('/{record}/edit'),
        ];
    }
}
