<?php

declare(strict_types=1);

namespace App\Enums;

enum PlanType: string
{
    case Free = 'free';
    case Pro = 'pro';
    case Agency = 'agency';

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Free Plan',
            self::Pro => 'Pro Plan',
            self::Agency => 'Agency Plan',
        };
    }

    public function maxClients(): int
    {
        return match ($this) {
            self::Free => 3,
            self::Pro => PHP_INT_MAX,
            self::Agency => PHP_INT_MAX,
        };
    }

    public function maxProjects(): int
    {
        return match ($this) {
            self::Free => 5,
            self::Pro => PHP_INT_MAX,
            self::Agency => PHP_INT_MAX,
        };
    }

    public function storageLimitBytes(): int
    {
        return match ($this) {
            self::Free => 500 * 1024 * 1024,    // 500 MB
            self::Pro => 5 * 1024 * 1024 * 1024,    // 5 GB
            self::Agency => 20 * 1024 * 1024 * 1024, // 20 GB
        };
    }

    public function apiRateLimitPerMinute(): ?int
    {
        return match ($this) {
            self::Free => null, // blocked
            self::Pro => 300,
            self::Agency => 300,
        };
    }

    public function hasApiAccess(): bool
    {
        return in_array($this, [self::Pro, self::Agency], true);
    }

    public function hasTeamFeature(): bool
    {
        return $this === self::Agency;
    }

    public function hasPresenceChannel(): bool
    {
        return $this === self::Agency;
    }

    public function hasMultiSheetExport(): bool
    {
        return $this === self::Agency;
    }

    public function hasAdvancedReports(): bool
    {
        return $this === self::Agency;
    }

    public function price(): int
    {
        return match ($this) {
            self::Free => 0,
            self::Pro => 19,
            self::Agency => 49,
        };
    }

    public function tokenTtlDays(): int
    {
        return match ($this) {
            self::Free,
            self::Pro => 30,
            self::Agency => 90,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Free => 'gray',
            self::Pro => 'blue',
            self::Agency => 'purple',
        };
    }
}
