<?php

namespace App\Enums;

enum NotificationChannel: string
{
    case Database = 'database';
    case Mail = 'mail';
    case Broadcast = 'broadcast';

    public function label(): string
    {
        return match ($this) {
            self::Database => 'In-App',
            self::Mail => 'Email',
            self::Broadcast => 'Real-time'
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Database => 'gray',
            self::Mail => 'blue',
            self::Broadcast => 'purple',
        };
    }
}
