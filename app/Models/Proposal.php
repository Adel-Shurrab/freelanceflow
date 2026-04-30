<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProposalStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'project_id',
    'user_id',
    'title',
    'content',
    'status',
    'expiry_days',
    'expires_at',
    'rejection_reason',
    'sent_at',
    'approved_at',
    'rejected_at',
])]
class Proposal extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => ProposalStatus::class,
            'expiry_days' => 'integer',
            'expires_at' => 'date',
            'sent_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
