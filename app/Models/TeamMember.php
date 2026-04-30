<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'agency_user_id',
    'member_user_id',
    'created_at',
])]
class TeamMember extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function agencyUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agency_user_id');
    }

    public function memberUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_user_id');
    }
}
