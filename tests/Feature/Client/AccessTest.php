<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

it('allows freelancers to access client management', function () {
    $freelancer = User::factory()->freelancer()->create();

    expect(Gate::forUser($freelancer)->allows('viewAny', Client::class))->toBeTrue();
    expect(Gate::forUser($freelancer)->allows('create', Client::class))->toBeTrue();
});

it('denies client users from accessing client management', function () {
    $clientUser = User::factory()->client()->create();

    expect(Gate::forUser($clientUser)->allows('viewAny', Client::class))->toBeFalse();
    expect(Gate::forUser($clientUser)->allows('create', Client::class))->toBeFalse();
});

it('allows a freelancer to manage their own client', function () {
    $freelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $freelancer->id,
    ]);

    expect(Gate::forUser($freelancer)->allows('view', $client))->toBeTrue();
    expect(Gate::forUser($freelancer)->allows('update', $client))->toBeTrue();
    expect(Gate::forUser($freelancer)->allows('delete', $client))->toBeTrue();
    expect(Gate::forUser($freelancer)->allows('archive', $client))->toBeTrue();
    expect(Gate::forUser($freelancer)->allows('restore', $client))->toBeTrue();
});

it('denies a freelancer from managing another freelancer client', function () {
    $freelancer = User::factory()->freelancer()->create();
    $otherFreelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $otherFreelancer->id,
    ]);

    expect(Gate::forUser($freelancer)->allows('view', $client))->toBeFalse();
    expect(Gate::forUser($freelancer)->allows('update', $client))->toBeFalse();
    expect(Gate::forUser($freelancer)->allows('delete', $client))->toBeFalse();
    expect(Gate::forUser($freelancer)->allows('archive', $client))->toBeFalse();
    expect(Gate::forUser($freelancer)->allows('restore', $client))->toBeFalse();
});

it('allows super admin to bypass client policy', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $freelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $freelancer->id,
    ]);

    expect(Gate::forUser($superAdmin)->allows('viewAny', Client::class))->toBeTrue();
    expect(Gate::forUser($superAdmin)->allows('create', Client::class))->toBeTrue();
    expect(Gate::forUser($superAdmin)->allows('view', $client))->toBeTrue();
    expect(Gate::forUser($superAdmin)->allows('update', $client))->toBeTrue();
    expect(Gate::forUser($superAdmin)->allows('delete', $client))->toBeTrue();
    expect(Gate::forUser($superAdmin)->allows('archive', $client))->toBeTrue();
    expect(Gate::forUser($superAdmin)->allows('restore', $client))->toBeTrue();
});

it('redirects guests away from clients pages', function () {
    $this->get(route('clients.index'))
        ->assertRedirect(route('login'))
    ;

    $this->get(route('clients.create'))
        ->assertRedirect(route('login'))
    ;
});

it('denies client users from viewing client management pages', function () {
    $clientUser = User::factory()->client()->create();

    $this->actingAs($clientUser)
        ->get(route('clients.index'))
        ->assertForbidden()
    ;

    $this->actingAs($clientUser)
        ->get(route('clients.create'))
        ->assertForbidden()
    ;
});

it('returns 404 when a freelancer tries to view another freelancer client', function () {
    $freelancer = User::factory()->freelancer()->create();
    $otherFreelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $otherFreelancer->id,
    ]);

    $this->actingAs($freelancer)
        ->get(route('clients.show', $client))
        ->assertNotFound()
    ;

    $this->actingAs($freelancer)
        ->get(route('clients.edit', $client))
        ->assertNotFound()
    ;
});
