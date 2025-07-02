<?php

namespace App\Filament\Resources\ExamSessionResource\Pages;

    use App\Filament\Resources\ExamSessionResource;
    use Filament\Actions\CreateAction;
    use Filament\Resources\Pages\ListRecords;

    class ListExamSessions extends ListRecords {
        protected static string $resource = ExamSessionResource::class;

        protected function getHeaderActions(): array {
        return [
        CreateAction::make(),
        ];
        }
    }
