<?php

namespace App\Enums;

enum ProposalStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Sent => 'Sent',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Expired => 'Expired'
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Sent => 'blue',
            self::Approved => 'green',
            self::Rejected => 'red',
            self::Expired => 'orange'
        };
    }

    public function isActive(): bool
    {
        return ! in_array($this, [self::Rejected, self::Expired]);
    }

    // For partial unique index logic
    public function isEditable(): bool
    {
        return $this === self::Draft;
    }
}
