<?php

namespace App\Filament\Resources\InstagramComments;

use App\Filament\Resources\InstagramComments\Pages\CreateInstagramComment;
use App\Filament\Resources\InstagramComments\Pages\EditInstagramComment;
use App\Filament\Resources\InstagramComments\Pages\ListInstagramComments;
use App\Filament\Resources\InstagramComments\Schemas\InstagramCommentForm;
use App\Filament\Resources\InstagramComments\Tables\InstagramCommentsTable;
use App\Models\InstagramComment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InstagramCommentResource extends Resource
{
    protected static ?string $model = InstagramComment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';  
    protected static ?string $navigationLabel = 'Komentar Instagram';
    protected static ?string $pluralModelLabel = 'Data Komentar Instagram';
    protected static string | UnitEnum | null $navigationGroup = 'Instagram';
    protected static ?string $navigationBadgeTooltip = 'Jumlah Komentar instagram';
    protected static ?string $recordTitleAttribute = 'authorDisplayName';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Schema $schema): Schema
    {
        return InstagramCommentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InstagramCommentsTable::configure($table);
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
            'index' => ListInstagramComments::route('/'),
            'create' => CreateInstagramComment::route('/create'),
            //'edit' => EditInstagramComment::route('/{record}/edit'),
        ];
    }
}
