<?php

namespace App\Enums;

enum UserGender: string
{
    case MALE = 'male';

    case FEMALE = 'female';

    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::MALE => trans('user_enums.genders.male'),
            self::FEMALE => trans('user_enums.genders.female'),
            self::OTHER => trans('user_enums.genders.other'),
        };
    }
}
