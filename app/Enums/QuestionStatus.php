<?php

namespace App\Enums;

enum QuestionStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => trans('questions_enum.status_pending'),
            self::APPROVED => trans('questions_enum.status_approved'),
            self::REJECTED => trans('questions_enum.status_rejected'),
        };
    }
}
