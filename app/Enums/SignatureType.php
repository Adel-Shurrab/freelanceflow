<?php

declare(strict_types=1);

namespace App\Enums;

enum SignatureType: string
{
    case TypedName = 'typed_name';

    public function label(): string
    {
        return 'Typed Name';
    }

    public function color(): string
    {
        return 'blue';
    }
}
