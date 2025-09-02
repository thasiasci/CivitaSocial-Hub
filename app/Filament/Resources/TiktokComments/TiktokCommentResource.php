<?php

namespace App\Filament\Resources\TiktokComments;

use App\Filament\Resources\TiktokComments\Pages\CreateTiktokComment;
use App\Filament\Resources\TiktokComments\Pages\EditTiktokComment;
use App\Filament\Resources\TiktokComments\Pages\ListTiktokComments;
use App\Filament\Resources\TiktokComments\Schemas\TiktokCommentForm;
use App\Filament\Resources\TiktokComments\Tables\TiktokCommentsTable;
use App\Models\TiktokComment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TiktokCommentResource extends Resource
{
    protected static ?string $model = TiktokComment::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';  
    protected static ?string $navigationLabel = 'Komentar Tiktok';
    protected static ?string $pluralModelLabel = 'Data Komentar Tiktok';
    protected static string | UnitEnum | null $navigationGroup = 'Komentar Tiktok';
    protected static ?string $navigationBadgeTooltip = 'Jumlah Komentar Tiktok';
    protected static ?string $recordTitleAttribute = 'authorDisplayName';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return TiktokCommentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TiktokCommentsTable::configure($table);
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
            'index' => ListTiktokComments::route('/'),
            'create' => CreateTiktokComment::route('/create'),
            //'edit' => EditTiktokComment::route('/{record}/edit'),
        ];
    }
}
