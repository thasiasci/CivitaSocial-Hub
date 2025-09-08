<?php

namespace App\Filament\Resources\InstagramComments\Tables;
use App\Models\InstagramComment;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Checkbox;
use League\Csv\Reader;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Form; 
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Carbon\Carbon;

class InstagramCommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('link_konten')
                    ->label('Url Konten')
                    ->searchable(),
                TextColumn::make('periode')
                    ->label('Periode')
                    ->searchable(),
                TextColumn::make('comment')
                    ->label('Komentar')
                    ->limit(100)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('bulan') 
                    ->label('Bulan')
                    ->searchable(),   
                TextColumn::make('sentimen') 
                    ->label('Sentimen')
                    ->sortable()
                    ->badge(), 
                TextColumn::make('is_spam')
                    ->label('Spam')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                         if (is_null($state)) {
                             return '-'; // belum ada label
                        }
                        return $state == 1 ? 'Spam' : 'Bukan Spam';
                    })
                    ->colors([
                             'danger' => fn ($state) => $state == 1,
                            'success' => fn ($state) => $state == 0,
                    ]),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                 ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make('labelSentimen')
                    ->label('Beri Label Sentimen')
                    ->modalHeading('Beri Label Sentimen')
                    ->modalSubmitActionLabel('Simpan')
                    ->modalCancelActionLabel('Batal')
                    ->form([
                        Textarea::make('comment')
                            ->label('Isi Komentar ')
                            ->disabled()
                            ->columnSpanFull(),
                        Select::make('sentimen')
                            ->options([
                                'Positif' => 'Positif',
                                'Negatif' => 'Negatif',
                                'Netral' => 'Netral',
                            ])
                            ->label('Sentimen')
                            ->required()
                            ->columnSpanFull(),
                        
                    ])
                    
                    ->action(function (array $data, InstagramComment $record): void {
                        $record->sentimen = $data['sentimen'];
                        $record->save();
                        
                        Notification::make()
                            ->title('Berhasil')
                            ->body('Label sentimen berhasil disimpan!')
                            ->success()
                            ->send();
                    }),
                    EditAction::make('labelSpam')
                    ->label('Beri Label Spam')
                    ->modalHeading('Beri Label Spam')
                    ->modalSubmitActionLabel('Simpan')
                    ->modalCancelActionLabel('Batal')
                    ->form([
                        Textarea::make('comment')
                                ->label('Isi Komentar')
                                ->disabled()
                                ->columnSpanFull(),
                            Select::make('is_spam')
                                ->options([
                                    0 => 'Bukan Spam',
                                    1 => 'Spam',
                                ])
                                ->label('Status Spam')
                                ->required()
                                ->columnSpanFull(),
                    ])
                    ->action(function (array $data, InstagramComment $record): void {
                        $record->is_spam = $data['is_spam'];
                        $record->save();
                        
                       
                    }),
                ])
                ->label('Aksi')
                ->size('sm')
                ->color('gray')
                ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
               
            ]);
    }
    
}
