<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable([
    'user_id',
    'client_id',
    'name',
    'description',
    'status',
    'deadline',
    'last_activity_at',
])]
class Project extends Model implements HasMedia
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'deadline' => 'date',
            'last_activity_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('project_files')
            ->useDisk('public')
        ;
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->deadline !== null
                && $this->deadline->isPast()
                && ! in_array($this->status, [
                    ProjectStatus::Completed,
                    ProjectStatus::Cancelled,
                ], true),
        );
    }
}
