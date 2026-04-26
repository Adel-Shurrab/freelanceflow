<?php

namespace App\Enums;

enum SignerType: string
{
    case Freelancer = 'freelancer';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::Freelancer => 'Freelancer',
            self::Client => 'Client',
        };
    }
}
