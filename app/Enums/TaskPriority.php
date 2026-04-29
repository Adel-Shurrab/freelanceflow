<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Urgent = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
            self::Urgent => 'Urgent',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Low => 'gray',
            self::Medium => 'blue',
            self::High => 'orange',
            self::Urgent => 'red',
        };
    }

    public function sortOrder(): int
    {
        return match ($this) {
            self::Urgent => 1,
            self::High => 2,
            self::Medium => 3,
            self::Low => 4,
        };
    }
}
