<?php

declare(strict_types=1);

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

    public static function defaultPreferences(): array
    {
        return [
            'milestone_completed' => [self::Database->value, self::Broadcast->value],
            'proposal_sent' => [self::Database->value, self::Mail->value],
            'proposal_approved' => [self::Database->value, self::Mail->value, self::Broadcast->value],
            'proposal_rejected' => [self::Database->value, self::Mail->value],
            'invoice_sent' => [self::Database->value, self::Mail->value],
            'invoice_paid' => [self::Database->value, self::Mail->value, self::Broadcast->value],
            'invoice_overdue' => [self::Database->value, self::Mail->value],
            'contract_signed' => [self::Database->value, self::Broadcast->value],
            'client_approved_proposal' => [self::Database->value, self::Broadcast->value],
            'new_message_sent' => [self::Database->value, self::Broadcast->value],
            'timelog_reminder' => [self::Database->value, self::Mail->value],
        ];
    }
}
