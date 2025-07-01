<?php

namespace App\Filament\Resources\QuizzResource\Pages;

use App\Filament\Resources\QuizzResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuizzs extends ListRecords
{
    protected static string $resource = QuizzResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
