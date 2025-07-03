<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestResource\Pages;
use App\Models\Test;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestResource extends Resource
{
    protected static ?string $model = Test::class;

    protected static ?string $slug = 'tests';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Exams Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('exam_session_id')
                    ->relationship('session', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Session d\'examen'),

                Select::make('quizz_id')
                    ->relationship('quizz', 'subject')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Questionnaire'),

                Select::make('user_id')
                    ->relationship('user', 'lastname')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Utilisateur inscrit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('session.name')
                    ->label('Session d\'examen')
                    ->sortable(),

                TextColumn::make('quizz.subject')
                    ->label('Questionnaire')
                    ->sortable(),

                TextColumn::make('user.lastname')
                    ->formatStateUsing(fn($state, $record) => $record->user->lastname . ' ' . $record->user->firstname)
                    ->label('Utilisateur')
                    ->sortable(),

                TextColumn::make('nb_pause')
                    ->label('Pauses')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTests::route('/'),
            'create' => Pages\CreateTest::route('/create'),
            'edit' => Pages\EditTest::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
