<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationResource\Pages;
use App\Models\Registration;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $slug = 'registrations';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Exams Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'email')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('exam_session_id')
                    ->label('Exam Session')
                    ->relationship('examSession', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Checkbox::make('no_show')
                    ->label('Non présent'),

                Select::make('registered_by')
                    ->relationship('registeredBy', 'email')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')
                    ->label('Utilisateur')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('examSession.name')
                    ->label('Session d\'examen')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('no_show')
                    ->label('Non présent')
                    ->boolean(),

                TextColumn::make('registeredBy.email')
                    ->label('Enregistré par')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime('d/m/Y H:i')
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
            'index' => Pages\ListRegistrations::route('/'),
            'create' => Pages\CreateRegistration::route('/create'),
            'edit' => Pages\EditRegistration::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['registeredBy', 'user', 'examSession']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['registeredBy.email', 'user.email', 'examSession.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->user) {
            $details['Utilisateur'] = $record->user->email;
        }

        if ($record->examSession) {
            $details['Session'] = $record->examSession->name;
        }

        if ($record->registeredBy) {
            $details['Enregistré par'] = $record->registeredBy->email;
        }

        return $details;
    }
}
