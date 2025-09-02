<?php

namespace App\Filament\Resources\KomentarBalasans;

use App\Filament\Resources\KomentarBalasans\Pages\CreateKomentarBalasan;
use App\Filament\Resources\KomentarBalasans\Pages\EditKomentarBalasan;
use App\Filament\Resources\KomentarBalasans\Pages\ListKomentarBalasans;
use App\Filament\Resources\KomentarBalasans\Pages\ViewKomentarBalasan;
use App\Filament\Resources\KomentarBalasans\Schemas\KomentarBalasanForm;
use App\Filament\Resources\KomentarBalasans\Schemas\KomentarBalasanInfolist;
use App\Filament\Resources\KomentarBalasans\Tables\KomentarBalasansTable;
use App\Models\KomentarBalasan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KomentarBalasanResource extends Resource
{
    protected static ?string $model = KomentarBalasan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Komentar Balasan';
    protected static ?string $pluralModelLabel = 'Data Komentar Balasan';
    protected static string | UnitEnum | null $navigationGroup = 'Komentar Youtube';
    protected static ?int $navigationSort = 2; 
    protected static ?string $navigationBadgeTooltip = 'Jumlah Komentar Balasan';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return KomentarBalasanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KomentarBalasanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KomentarBalasansTable::configure($table);
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
            'index' => ListKomentarBalasans::route('/'),
            //'create' => CreateKomentarBalasan::route('/create'),
            'view' => ViewKomentarBalasan::route('/{record}'),
            //'edit' => EditKomentarBalasan::route('/{record}/edit'),
        ];
    }
}
