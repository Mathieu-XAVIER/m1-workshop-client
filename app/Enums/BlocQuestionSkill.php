<?php

namespace App\Enums;

enum BlocQuestionSkill: string
{
    case WRITING = 'writing';
    case READING = 'reading';
    case LISTENING = 'listening';

    case SPEAKING = 'speaking';

    public function label(): string
    {
        return match ($this) {
            self::WRITING => trans('bloc_question_enum.skill_writing'),
            self::READING => trans('bloc_question_enum.skill_reading'),
            self::LISTENING => trans('bloc_question_enum.skill_listening'),
            self::SPEAKING => trans('bloc_question_enum.skill_speaking'),
        };
    }
}
