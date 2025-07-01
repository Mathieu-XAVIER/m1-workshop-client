<?php

namespace App\Filament\Resources\QuizzResource\Pages;

use App\Filament\Resources\QuizzResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditQuizz extends EditRecord
{
    protected static string $resource = QuizzResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
