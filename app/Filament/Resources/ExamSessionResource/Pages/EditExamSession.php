<?php

namespace App\Filament\Resources\ExamSessionResource\Pages;

    use App\Filament\Resources\ExamSessionResource;
    use Filament\Actions\DeleteAction;
    use Filament\Actions\ForceDeleteAction;
    use Filament\Actions\RestoreAction;
    use Filament\Resources\Pages\EditRecord;

    class EditExamSession extends EditRecord {
        protected static string $resource = ExamSessionResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
ForceDeleteAction::make(),
RestoreAction::make(),
        ];
        }
    }
