<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamSessionResource\Pages;
use App\Models\ExamSession;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExamSessionResource extends Resource
{
    protected static ?string $model = ExamSession::class;

    protected static ?string $slug = 'exam-sessions';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Exams Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                DateTimePicker::make('date')
                    ->label('Date et heure')
                    ->required(),
                Select::make('location_id')
                    ->relationship('location', 'name')
                    ->required()
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nom du lieu'),
                        TextInput::make('adress_line_1')
                            ->label('Adresse ligne 1')
                            ->required(),
                        TextInput::make('adress_line_2')
                            ->label('Adresse ligne 2'),
                        TextInput::make('city')
                            ->label('Ville')
                            ->required(),
                        TextInput::make('zip_code')
                            ->label('Code postal')
                            ->required(),
                    ]),
                TextInput::make('capacity')
                    ->label('Capacité')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('location.name')
                    ->label('Lieu'),
                TextColumn::make('capacity')
                    ->label('Capacité'),
                TextColumn::make('registrations_count')
                    ->counts('registrations')
                    ->label('Inscrits'),
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
            'index' => Pages\ListExamSessions::route('/'),
            'create' => Pages\CreateExamSession::route('/create'),
            'edit' => Pages\EditExamSession::route('/{record}/edit'),
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
        return [];
    }
}
