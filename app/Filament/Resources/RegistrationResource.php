<?php

namespace App\Filament\Resources;

    use App\Filament\Resources\RegistrationResource\Pages;
    use App\Models\Registration;
    use Filament\Forms\Components\Checkbox;
    use Filament\Forms\Components\Placeholder;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables\Actions\BulkActionGroup;
    use Filament\Tables\Actions\DeleteAction;
    use Filament\Tables\Actions\DeleteBulkAction;
    use Filament\Tables\Actions\EditAction;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Table;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;

    class RegistrationResource extends Resource {
        protected static ?string $model = Registration::class;

        protected static ?string $slug = 'registrations';

        protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

        PUBLIC static function form(Form $form): Form
        {
        return $form
        ->schema([//
        Select::make('user_id')
        ->relationship('user', 'email')
        ->searchable()
        ->required(),

        TextInput::make('session_id')
        ->required()
        ->integer(),

        Checkbox::make('no_show'),

        Select::make('registered_by')
        ->relationship('registeredBy', 'email')
        ->searchable()
        ->required(),

        Placeholder::make('created_at')
        ->label('Created Date')
        ->content(fn (?Registration $record): string => $record?->created_at?->diffForHumans() ?? '-'),

        Placeholder::make('updated_at')
        ->label('Last Modified Date')
        ->content(fn (?Registration $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
        }

        PUBLIC static function table(Table $table): Table
        {
        return $table
        ->columns([
        TextColumn::make('user.email')
        ->searchable()
        ->sortable(),

        TextColumn::make('session_id'),

        TextColumn::make('no_show'),

        TextColumn::make('registeredBy.email')
        ->searchable()
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

        PUBLIC static function getGlobalSearchEloquentQuery(): Builder
        {
        return parent::getGlobalSearchEloquentQuery()->with(['registeredBy', 'user']);
        }

        PUBLIC static function getGloballySearchableAttributes(): array
        {
        return ['registeredBy.email', 'user.email'];
        }

        PUBLIC static function getGlobalSearchResultDetails(Model $record): array
        {
        $details = [];

        if ($record->registeredBy) {
$details['RegisteredBy'] = $record->registeredBy->email;}

if ($record->user) {
$details['User'] = $record->user->email;}

        return $details;
        }
    }
