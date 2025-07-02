<?php

namespace App\Enums;

enum TrainingStatus: string
{
    case TODO = 'to-do';

    case DONE = 'done';

    public function label(): string
    {
        return match ($this) {
            self::TODO => 'A faire',
            self::DONE => 'Réalisé',
        };
    }
}
