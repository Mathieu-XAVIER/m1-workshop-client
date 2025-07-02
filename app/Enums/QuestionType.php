<?php

namespace App\Enums;

enum QuestionType: string
{
    case MULTIPLE_CHOICE = 'multiple_choice';
    case TRUE_FALSE = 'true_false';
    case FILL_IN_THE_BLANK = 'fill_in_the_blank';
    case SHORT_ANSWER = 'short_answer';
    case LONG_ANSWER = 'long_answer';

    public function label(): string
    {
        return match ($this) {
            self::MULTIPLE_CHOICE => 'Choix multiple',
            self::TRUE_FALSE => 'Vrai ou Faux',
            self::FILL_IN_THE_BLANK => 'Remplir le blanc',
            self::SHORT_ANSWER => 'Réponse courte',
            self::LONG_ANSWER => 'Réponse longue',
        };
    }
}
