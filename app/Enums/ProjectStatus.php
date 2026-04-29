<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case OnHold = 'on_hold';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Active => 'Active',
            self::OnHold => 'On Hold',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',

        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Active => 'green',
            self::OnHold => 'yellow',
            self::Completed => 'blue',
            self::Cancelled => 'red',
        };
    }

    /** @return ProjectStatus[] */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft => [self::Active, self::Cancelled],
            self::Active => [self::OnHold, self::Completed, self::Cancelled],
            self::OnHold => [self::Active, self::Cancelled],
            self::Completed => [self::Cancelled],
            self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions());
    }

    public function allowsProposals(): bool
    {
        return $this === self::Active;
    }

    public function allowsNewInvoices(): bool
    {
        return $this === self::Active;
    }

    public function allowsTimeEntries(): bool
    {
        return $this === self::Active;
    }

    public function allowsPaymentsOnExistingInvoices(): bool
    {
        return in_array($this, [
            self::Active,
            self::OnHold,
            self::Completed,
            self::Cancelled,
        ]);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled]);
    }
}
