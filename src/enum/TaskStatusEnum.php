<?php

namespace App\enum;

enum TaskStatusEnum: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';

    public function toString(): string
    {
        return $this->value;
    }
}
