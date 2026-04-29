<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceStatus;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable([
    'user_id',
    'project_id',
    'invoice_number',
    'status',
    'total_amount',
    'due_date',
    'currency',
    'reminder_count',
    'notes',
    'sent_at',
    'paid_at',
])]
class Invoice extends Model implements HasMedia
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $casts = [
        'status' => InvoiceStatus::class,
        'total_amount' => 'decimal:2',
        'due_date' => 'date',
        'reminder_count' => 'integer',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('invoice_pdfs')
            ->singleFile()
            ->useDisk('local')
            ->acceptsMimeTypes(['application/pdf'])
        ;
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->due_date !== null
                && $this->due_date->isPast()
                && $this->status === InvoiceStatus::Sent,
        );
    }

    protected function isLocked(): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->status->locksTimeEntries(),
        );
    }
}
