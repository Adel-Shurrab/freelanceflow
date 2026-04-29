<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SignatureType;
use App\Enums\SignerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Signature extends Model
{
    use HasFactory;

    public $timestamps = false;
    public const UPDATED_AT = null;

    protected $fillable = [
        'signable_type',
        'signable_id',
        'signer_type',
        'signer_id',
        'signature_type',
        'signature_data',
        'ip_address',
        'user_agent',
        'signed_at',
        'created_at',
    ];

    protected $casts = [
        'signature_type' => SignatureType::class,
        'signer_type' => SignerType::class,
        'signed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function signable(): MorphTo
    {
        return $this->morphTo();
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signer_id');
    }
}
