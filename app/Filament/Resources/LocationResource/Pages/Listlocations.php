<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\locationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class Listlocations extends ListRecords
{
    protected static string $resource = locationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
