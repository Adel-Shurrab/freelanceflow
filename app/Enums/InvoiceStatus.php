<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Paid = 'paid';
    case Overdue = 'overdue'; // Child state of Sent
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Sent => 'Sent',
            self::Paid => 'Paid',
            self::Overdue => 'Overdue',
            self::Cancelled => 'Cancelled'
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Sent => 'blue',
            self::Paid => 'green',
            self::Overdue => 'red',
            self::Cancelled => 'gray'
        };
    }

    // Time entries are READ-ONLY when invoice is in these states
    public function locksTimeEntries(): bool
    {
        return in_array($this, [self::Sent, self::Overdue, self::Paid]);
    } // BR-INV-04

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft => [self::Sent, self::Cancelled],
            self::Sent => [self::Paid, self::Overdue, self::Cancelled],
            self::Overdue => [self::Paid],
            self::Paid => [], // Terminal
            self::Cancelled => [], // Terminal
        };
    }
}
