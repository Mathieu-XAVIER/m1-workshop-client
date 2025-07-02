<?php

namespace App\Filament\Resources;

    use App\Filament\Resources\TestResource\Pages;
    use App\Models\Test;
    use Filament\Forms\Components\Placeholder;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables\Actions\BulkActionGroup;
    use Filament\Tables\Actions\DeleteAction;
    use Filament\Tables\Actions\DeleteBulkAction;
    use Filament\Tables\Actions\EditAction;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Table;

    class TestResource extends Resource {
        protected static ?string $model = Test::class;

        protected static ?string $slug = 'tests';

        protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

        PUBLIC static function form(Form $form): Form
        {
        return $form
        ->schema([//
        TextInput::make('session_id')
        ->required()
        ->integer(),

        TextInput::make('quizz_id')
        ->required()
        ->integer(),

        Placeholder::make('created_at')
        ->label('Created Date')
        ->content(fn (?Test $record): string => $record?->created_at?->diffForHumans() ?? '-'),

        Placeholder::make('updated_at')
        ->label('Last Modified Date')
        ->content(fn (?Test $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
        }

        PUBLIC static function table(Table $table): Table
        {
        return $table
        ->columns([
        TextColumn::make('session_id'),

        TextColumn::make('quizz_id'),
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

        PUBLIC static function getGloballySearchableAttributes(): array
        {
        return [];
        }
    }
