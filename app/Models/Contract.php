<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContractStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable([
    'proposal_id',
    'project_id',
    'user_id',
    'status',
    'content',
    'sent_at',
    'fully_signed_at',
])]
class Contract extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'status' => ContractStatus::class,
            'sent_at' => 'datetime',
            'fully_signed_at' => 'datetime',
        ];
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function signatures(): MorphMany
    {
        return $this->morphMany(Signature::class, 'signable');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('signed_contracts')
            ->singleFile()
            ->useDisk('private')
        ;
    }

    public function signatureCount(): int
    {
        return $this->signatures()->count();
    }
}
