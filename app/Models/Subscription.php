<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlanType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'plan_type',
    'started_at',
    'expires_at',
    'upgraded_from',
    'payment_reference',
    'amount_paid',
    'currency',
])]
class Subscription extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'plan_type' => PlanType::class,
            'upgraded_from' => PlanType::class,
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
            'amount_paid' => 'decimal:8,2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
