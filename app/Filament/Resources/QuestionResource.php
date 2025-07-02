<?php

namespace App\Filament\Resources;

use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers\BlocsRelationManager;
use App\Models\Question;
use Filament\Forms\Components\TextInput;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $slug = 'questions';

    protected static ?string $navigationGroup = 'Quizz Management';

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required(),

                TextInput::make('status')
                    ->required(),

                TextInput::make('type')
                    ->required(),

                TextInput::make('level')
                    ->required()
                    ->integer(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->formatStateUsing(fn(QuestionType $state): string => $state->label())
                    ->searchable()
                    ->sortable(),

                IconColumn::make('status')
                    ->icon(fn(QuestionStatus $state): string => match ($state) {
                        QuestionStatus::PENDING => 'heroicon-o-clock',
                        QuestionStatus::APPROVED => 'heroicon-o-check-circle',
                        QuestionStatus::REJECTED => 'heroicon-o-x-circle',
                    })
                    ->color(fn(QuestionStatus $state): string => match ($state) {
                        QuestionStatus::PENDING => 'warning',
                        QuestionStatus::APPROVED => 'success',
                        QuestionStatus::REJECTED => 'danger',
                    })
                    ->tooltip(fn(QuestionStatus $state): string => $state->label()),

                TextColumn::make('level')
                    ->sortable()
                    ->label('Niveau'),

                TextColumn::make('blocs_count')
                    ->counts('blocs')
                    ->label('Utilisée dans'),
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            BlocsRelationManager::class,
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
        return ['title'];
    }
}
