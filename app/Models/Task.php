<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable([
    'milestone_id',
    'assigned_to',
    'name',
    'description',
    'status',
    'priority',
    'due_date',
])]
class Task extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'due_date' => 'date',
        ];
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('task_files')
            ->useDisk('public')
        ;
    }

    public function isDone(): bool
    {
        return $this->status === TaskStatus::Done;
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->due_date !== null
                && $this->due_date->isPast()
                && $this->status !== TaskStatus::Done,
        );
    }
}
