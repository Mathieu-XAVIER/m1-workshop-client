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
            self::MULTIPLE_CHOICE => trans('questions_enum.type_multiple_choice'),
            self::TRUE_FALSE => trans('questions_enum.type_true_false'),
            self::FILL_IN_THE_BLANK => trans('questions_enum.type_fill_in_the_blank'),
            self::SHORT_ANSWER => trans('questions_enum.type_short_answer'),
            self::LONG_ANSWER => trans('questions_enum.type_long_answer'),
        };
    }
}
