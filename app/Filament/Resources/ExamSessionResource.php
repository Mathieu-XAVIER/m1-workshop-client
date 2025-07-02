<?php

namespace App\Filament\Resources;

    use App\Filament\Resources\ExamSessionResource\Pages;
    use App\Models\ExamSession;
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
    use Filament\Tables\Filters\TrashedFilter;
    use Filament\Tables\Table;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\SoftDeletingScope;

    class ExamSessionResource extends Resource {
        protected static ?string $model = ExamSession::class;

        protected static ?string $slug = 'exam-sessions';

        protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

        PUBLIC static function form(Form $form): Form
        {
        return $form
        ->schema([//
        ]);
        }

        PUBLIC static function table(Table $table): Table
        {
        return $table
        ->columns([
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

        PUBLIC static function getEloquentQuery(): Builder
        {
        return parent::getEloquentQuery()
        ->withoutGlobalScopes([
        SoftDeletingScope::class,
        ]);
        }

        PUBLIC static function getGloballySearchableAttributes(): array
        {
        return [];
        }
    }
