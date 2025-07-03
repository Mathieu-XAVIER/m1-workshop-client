<?php

namespace App\Filament\Resources;

use App\Enums\BlocQuestionSkill;
use App\Filament\Resources\BlocResource\Pages;
use App\Filament\Resources\BlocResource\RelationManagers\QuestionsRelationManager;
use App\Models\Bloc;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlocResource extends Resource
{
    protected static ?string $model = Bloc::class;

    protected static ?string $slug = 'blocs';

    protected static ?string $navigationGroup = 'Quizz Management';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),

                Select::make('quizz_id')
                    ->relationship('quizz', 'subject')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('skill')
                    ->options([
                        BlocQuestionSkill::SPEAKING->value => 'Speaking',
                        BlocQuestionSkill::WRITING->value => 'Writing',
                        BlocQuestionSkill::READING->value => 'Reading',
                        BlocQuestionSkill::LISTENING->value => 'Listening',
                    ])
                    ->label('Compétence')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label('Nombre de questions'),

                TextColumn::make('quizz.subject')
                    ->searchable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlocs::route('/'),
            'create' => Pages\CreateBloc::route('/create'),
            'edit' => Pages\EditBloc::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            QuestionsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
