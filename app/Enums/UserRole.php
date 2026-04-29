<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Freelancer = 'freelancer';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Freelancer => 'Freelancer',
            self::Client => 'Client',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SuperAdmin => 'red',
            self::Admin => 'purple',
            self::Freelancer => 'blue',
            self::Client => 'green',
        };
    }

    public function isAdminRole(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Admin], true);
    }
}
