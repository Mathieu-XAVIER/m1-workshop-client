<?php

namespace App\Filament\Resources\ExamSessionResource\Pages;

use App\Filament\Resources\ExamSessionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExamSession extends CreateRecord
{
    protected static string $resource = ExamSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
