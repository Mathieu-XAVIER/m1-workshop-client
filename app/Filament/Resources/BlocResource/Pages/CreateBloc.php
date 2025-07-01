<?php

namespace App\Filament\Resources\BlocResource\Pages;

use App\Filament\Resources\BlocResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBloc extends CreateRecord
{
    protected static string $resource = BlocResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
