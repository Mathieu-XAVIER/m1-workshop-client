<?php

namespace App\Filament\Resources;

use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers\BlocsRelationManager;
use App\Models\Bloc;
use App\Models\Question;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
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
use Illuminate\Database\Eloquent\Collection;
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

                Select::make('status')
                    ->options(collect(QuestionStatus::cases())
                        ->mapWithKeys(fn(QuestionStatus $status) => [$status->value => $status->label()]))
                    ->required()
                    ->visibleOn('edit'),

                Select::make('type')
                    ->options(collect(QuestionType::cases())
                        ->mapWithKeys(fn(QuestionType $type) => [$type->value => $type->label()]))
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, $livewire) {
                        $livewire->form->fill([
                            'type' => $state,
                            'content' => [],
                            'answer' => [],
                        ]);
                    }),

                Select::make('level')
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                        6 => '6',
                        7 => '7',
                        8 => '8',
                        9 => '9',
                    ])
                    ->required(),

                Section::make('Contenu de la question')
                    ->key('contentWrapper')
                    ->schema(fn(Get $get) => self::getContentFields($get('type')))
                    ->columns(1),

                Section::make('Réponse')
                    ->schema(fn(Get $get) => self::getAnswerFields($get('type')))
                    ->columns(1),
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
                    BulkAction::make('attachToBloc')
                        ->label('Rattacher à un bloc')
                        ->icon('heroicon-o-link')
                        ->form([
                            Select::make('bloc_id')
                                ->label('Bloc')
                                ->options(Bloc::query()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $bloc = Bloc::find($data['bloc_id']);

                            if (!$bloc) {
                                Notification::make()
                                    ->title('Erreur')
                                    ->body('Bloc non trouvé.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $questionsAttached = 0;

                            foreach ($records as $question) {
                                if (!$bloc->questions()->where('question_id', $question->id)->exists()) {
                                    $bloc->questions()->attach($question);
                                    $questionsAttached++;
                                }
                            }

                            Notification::make()
                                ->title('Questions rattachées')
                                ->body("{$questionsAttached} questions ont été rattachées au bloc {$bloc->name}.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
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

    private static function getContentFields(?string $type): array
    {
        if (!$type) {
            return [];
        }

        $questionType = QuestionType::from($type);

        return match ($questionType) {
            QuestionType::MULTIPLE_CHOICE => [
                Textarea::make('content.question')
                    ->label('Question')
                    ->required(),
                Repeater::make('content.options')
                    ->label('Options')
                    ->schema([
                        TextInput::make('text')
                            ->label('Texte de l\'option')
                            ->required(),
                    ])
                    ->required()
                    ->minItems(2)
                    ->defaultItems(4),
            ],
            QuestionType::TRUE_FALSE, QuestionType::SHORT_ANSWER, QuestionType::LONG_ANSWER => [
                Textarea::make('content.question')
                    ->label('Question')
                    ->required(),
            ],
            QuestionType::FILL_IN_THE_BLANK => [
                Textarea::make('content.text')
                    ->label('Texte avec espaces à remplir')
                    ->helperText('Utilisez [blank] pour indiquer un espace à remplir')
                    ->required(),
            ],
        };
    }

    private static function getAnswerFields(?string $type): array
    {
        if (!$type) {
            return [];
        }

        $questionType = QuestionType::from($type);

        return match ($questionType) {
            QuestionType::MULTIPLE_CHOICE => [
                Repeater::make('answer.options')
                    ->label('Réponses possibles')
                    ->schema([
                        TextInput::make('text')
                            ->label('Texte de la réponse')
                            ->required(),
                        Radio::make('correct')
                            ->label('Réponse correcte ?')
                            ->options([
                                true => 'Oui',
                                false => 'Non',
                            ])
                            ->required(),
                    ])
                    ->required()
                    ->defaultItems(4),
            ],
            QuestionType::TRUE_FALSE => [
                Radio::make('answer.correct')
                    ->label('Réponse correcte')
                    ->options([
                        true => 'Vrai',
                        false => 'Faux',
                    ])
                    ->required(),
            ],
            QuestionType::FILL_IN_THE_BLANK => [
                Repeater::make('answer.blanks')
                    ->label('Réponses pour les blancs')
                    ->schema([
                        TextInput::make('value')
                            ->label('Réponse valide')
                            ->required(),
                    ])
                    ->required(),
            ],
            QuestionType::SHORT_ANSWER, QuestionType::LONG_ANSWER => [
                Textarea::make('answer.correct_answer')
                    ->label('Réponse correcte')
                    ->required(),
            ],
        };
    }
}
