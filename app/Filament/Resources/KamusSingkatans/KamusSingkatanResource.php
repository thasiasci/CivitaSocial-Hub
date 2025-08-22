<?php

namespace App\Filament\Resources\KamusSingkatans;

use App\Filament\Resources\KamusSingkatans\Pages\CreateKamusSingkatan;
use App\Filament\Resources\KamusSingkatans\Pages\EditKamusSingkatan;
use App\Filament\Resources\KamusSingkatans\Pages\ListKamusSingkatans;
use App\Filament\Resources\KamusSingkatans\Pages\ViewKamusSingkatan;
use App\Filament\Resources\KamusSingkatans\Schemas\KamusSingkatanForm;
use App\Filament\Resources\KamusSingkatans\Schemas\KamusSingkatanInfolist;
use App\Filament\Resources\KamusSingkatans\Tables\KamusSingkatansTable;
use App\Models\KamusSingkatan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KamusSingkatanResource extends Resource
{
    protected static ?string $model = KamusSingkatan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Kamus Singkatan';
    protected static ?string $pluralModelLabel = 'Daftar Singkatan';
    protected static string | UnitEnum | null $navigationGroup = 'Kamus';
    protected static ?string $navigationBadgeTooltip = 'Jumlah Singkatan';
     public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return KamusSingkatanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KamusSingkatanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KamusSingkatansTable::configure($table);
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
            'index' => ListKamusSingkatans::route('/'),
            'create' => CreateKamusSingkatan::route('/create'),
            'view' => ViewKamusSingkatan::route('/{record}'),
            'edit' => EditKamusSingkatan::route('/{record}/edit'),
        ];
    }
}
