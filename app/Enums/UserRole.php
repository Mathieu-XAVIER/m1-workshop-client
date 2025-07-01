<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';

    case USER = 'user';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => trans('user_enums.roles.admin'),
            self::USER => trans('user_enums.roles.user'),
        };
    }
}
