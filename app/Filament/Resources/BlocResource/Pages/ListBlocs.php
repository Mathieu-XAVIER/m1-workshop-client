<?php

namespace App\Filament\Resources\BlocResource\Pages;

use App\Filament\Resources\BlocResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBlocs extends ListRecords
{
    protected static string $resource = BlocResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
