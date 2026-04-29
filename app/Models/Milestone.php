<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MilestoneStatus;
use Database\Factories\MilestoneFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'name',
    'description',
    'status',
    'position',
    'due_date',
])]
class Milestone extends Model
{
    /** @use HasFactory<MilestoneFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => MilestoneStatus::class,
            'due_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->due_date !== null
                && $this->due_date->isPast()
                && $this->status !== MilestoneStatus::Completed,
        );
    }
}
