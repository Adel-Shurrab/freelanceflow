<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\PlanType;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

#[Fillable([
    'name',
    'email',
    'password',
    'plan_type',
    'role',
    'phone',
    'bio',
    'timezone',
    'language',
    'notification_preferences',
    'last_login_at',
])]

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements HasMedia, FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use InteractsWithMedia;
    use Notifiable;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
            'plan_type' => PlanType::class,
            'notification_preferences' => 'array',
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'plan_started_at' => 'datetime',
            'plan_expires_at' => 'datetime',
            'suspended_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function portalClients(): HasMany
    {
        return $this->hasMany(Client::class, 'client_user_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function sentInvitations(): HasMany
    {
        return $this->hasMany(ClientInvitation::class, 'invited_by');
    }

    public function receivedClientInvitations(): HasMany
    {
        return $this->hasMany(ClientInvitation::class, 'client_user_id');
    }

    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'team_members',
            'agency_user_id',
            'member_user_id',
        );
    }

    public function agencyAdmins(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'team_members',
            'member_user_id',
            'agency_user_id',
        );
    }

    public function projectMemberships(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot(['assigned_at', 'assigned_by'])
        ;
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatars')
            ->singleFile()
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif'])
            ->withResponsiveImages()
        ;
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(Signature::class, 'signer_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role->isAdminRole() || $this->role === UserRole::Freelancer;
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->getFirstMediaUrl('avatars')
                ?: 'https://ui-avatars.com/api/?name='.urlencode($this->name),
        );
    }
}
