<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'milestone_id',
    'completed_at',
    'total_tasks',
    'total_hours',
    'created_at',
])]
class MilestoneSummary extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
            'total_tasks' => 'integer',
            'total_hours' => 'decimal:2',
        ];
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }
}
