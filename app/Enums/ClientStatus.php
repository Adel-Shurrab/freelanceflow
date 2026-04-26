<?php

namespace App\Enums;

enum ClientStatus: string
{
    case Active = 'active'; // Counts toward plan limit
    case Archived = 'archived'; // Does NOT count — BR-CLIENT-01

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Archived => 'Archived'
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'green',
            self::Archived => 'gray'
        };
    }

    public function countsAgainstLimit(): bool
    {
        return $this === self::Active;
    }

    public function canReceiveProposals(): bool
    {
        return $this === self::Active;
    }
}
