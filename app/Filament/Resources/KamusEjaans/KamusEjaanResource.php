<?php

namespace App\Filament\Resources\KamusEjaans;

use App\Filament\Resources\KamusEjaans\Pages\CreateKamusEjaan;
use App\Filament\Resources\KamusEjaans\Pages\EditKamusEjaan;
use App\Filament\Resources\KamusEjaans\Pages\ListKamusEjaans;
use App\Filament\Resources\KamusEjaans\Pages\ViewKamusEjaan;
use App\Filament\Resources\KamusEjaans\Schemas\KamusEjaanForm;
use App\Filament\Resources\KamusEjaans\Schemas\KamusEjaanInfolist;
use App\Filament\Resources\KamusEjaans\Tables\KamusEjaansTable;
use App\Models\KamusEjaan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KamusEjaanResource extends Resource
{
    protected static ?string $model = KamusEjaan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Kamus Ejaan';
    protected static ?string $pluralModelLabel = 'Daftar Ejaan';
    protected static ?string $navigationBadgeTooltip = 'Jumlah Ejaan';
     public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    

    protected static string | UnitEnum | null $navigationGroup = 'Kamus';

    public static function form(Schema $schema): Schema
    {
        return KamusEjaanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KamusEjaanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KamusEjaansTable::configure($table);
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
            'index' => ListKamusEjaans::route('/'),
            'create' => CreateKamusEjaan::route('/create'),
            'view' => ViewKamusEjaan::route('/{record}'),
            'edit' => EditKamusEjaan::route('/{record}/edit'),
        ];
    }
}
