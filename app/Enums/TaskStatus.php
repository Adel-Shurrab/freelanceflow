<?php

namespace App\Enums;

enum TaskStatus: string
{
    case ToDo = 'to_do';
    case InProgress = 'in_progress';
    case InReview = 'in_review';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::ToDo => 'To Do',
            self::InProgress => 'In Progress',
            self::InReview => 'In Review',
            self::Done => 'Done'
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ToDo => 'gray',
            self::InProgress => 'yellow',
            self::InReview => 'blue',
            self::Done => 'green'
        };
    }

    public function triggersMilestoneCheck(): bool
    {
        return $this === self::Done;
    }
}
