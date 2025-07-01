<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\locationResource;
use Filament\Resources\Pages\CreateRecord;

class Createlocation extends CreateRecord
{
    protected static string $resource = locationResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
