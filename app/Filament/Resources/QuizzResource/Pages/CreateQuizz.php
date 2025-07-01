<?php

namespace App\Filament\Resources\QuizzResource\Pages;

use App\Filament\Resources\QuizzResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuizz extends CreateRecord
{
    protected static string $resource = QuizzResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
