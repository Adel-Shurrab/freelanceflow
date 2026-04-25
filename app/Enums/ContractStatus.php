<?php

namespace App\Enums;

enum ContractStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case PartiallySigned = 'partially_signed';
    case FullySigned = 'fully_signed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft', 
            self::Sent => 'Sent', 
            self::PartiallySigned => 'Partially Signed', 
            self::FullySigned => 'Fully Signed'
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray', 
            self::Sent => 'blue', 
            self::PartiallySigned => 'yellow', 
            self::FullySigned => 'green'
        };
    }

    public function isFullySigned(): bool
    {
        return $this === self::FullySigned;
    }
}
