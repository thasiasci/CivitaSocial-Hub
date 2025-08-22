<?php

namespace App\Filament\Resources\OpdChannels;

use App\Filament\Resources\OpdChannels\Pages\CreateOpdChannel;
use App\Filament\Resources\OpdChannels\Pages\EditOpdChannel;
use App\Filament\Resources\OpdChannels\Pages\ListOpdChannels;
use App\Filament\Resources\OpdChannels\Pages\ViewOpdChannel;
use App\Filament\Resources\OpdChannels\Schemas\OpdChannelForm;
use App\Filament\Resources\OpdChannels\Schemas\OpdChannelInfolist;
use App\Filament\Resources\OpdChannels\Tables\OpdChannelsTable;
use App\Models\OpdChannel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OpdChannelResource extends Resource
{
    protected static ?string $model = OpdChannel::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';
    protected static ?string $navigationLabel = 'OPD Channels';
    protected static ?string $pluralModelLabel = 'Data Channel Organisasi Perangkat Daerah';
    protected static ?string $navigationBadgeTooltip = 'Jumlah OPD Channels';
     public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return OpdChannelForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OpdChannelInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OpdChannelsTable::configure($table);
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
            'index' => ListOpdChannels::route('/'),
            'create' => CreateOpdChannel::route('/create'),
            'view' => ViewOpdChannel::route('/{record}'),
            'edit' => EditOpdChannel::route('/{record}/edit'),
        ];
    }
}
