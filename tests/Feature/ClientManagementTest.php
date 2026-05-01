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
});

it('redirects guests away from clients pages', function () {
    $this->get(route('clients.index'))
        ->assertRedirect(route('login'));

    $this->get(route('clients.create'))
        ->assertRedirect(route('login'));
});

it('denies client users from viewing client management pages', function () {
    $clientUser = User::factory()->client()->create();

    $this->actingAs($clientUser)
        ->get(route('clients.index'))
        ->assertForbidden();

    $this->actingAs($clientUser)
        ->get(route('clients.create'))
        ->assertForbidden();
});

it('allows a freelancer to create a client', function () {
    $freelancer = User::factory()->freelancer()->create();

    $response = $this->actingAs($freelancer)
        ->post(route('clients.store'), [
            'name' => 'Acme Corp',
            'email' => 'client@acme.test',
            'phone' => '+970599000000',
            'company' => 'Acme',
            'notes' => 'Important client.',
        ]);

    $client = Client::query()
        ->where('email', 'client@acme.test')
        ->firstOrFail();

    $response->assertRedirect(route('clients.show', $client));

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'user_id' => $freelancer->id,
        'name' => 'Acme Corp',
        'email' => 'client@acme.test',
        'company' => 'Acme',
        'status' => 'active',
    ]);
});

it('validates client creation data', function () {
    $freelancer = User::factory()->freelancer()->create();

    $this->actingAs($freelancer)
        ->from(route('clients.create'))
        ->post(route('clients.store'), [
            'name' => '',
            'email' => 'not-an-email',
            'phone' => str_repeat('1', 31),
            'company' => str_repeat('a', 151),
            'notes' => str_repeat('a', 5001),
        ])
        ->assertRedirect(route('clients.create'))
        ->assertSessionHasErrors([
            'name',
            'email',
            'phone',
            'company',
            'notes',
        ]);
});

it('allows different freelancers to use the same client email', function () {
    $freelancer = User::factory()->freelancer()->create();
    $otherFreelancer = User::factory()->freelancer()->create();

    Client::factory()->create([
        'user_id' => $otherFreelancer->id,
        'email' => 'shared@example.com',
    ]);

    $this->actingAs($freelancer)
        ->post(route('clients.store'), [
            'name' => 'Shared Email Client',
            'email' => 'shared@example.com',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('clients', [
        'user_id' => $freelancer->id,
        'email' => 'shared@example.com',
    ]);

    $this->assertDatabaseHas('clients', [
        'user_id' => $otherFreelancer->id,
        'email' => 'shared@example.com',
    ]);
});

it('returns 404 when a freelancer tries to view another freelancer client', function () {
    $freelancer = User::factory()->freelancer()->create();
    $otherFreelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $otherFreelancer->id,
    ]);

    $this->actingAs($freelancer)
        ->get(route('clients.show', $client))
        ->assertNotFound();

    $this->actingAs($freelancer)
        ->get(route('clients.edit', $client))
        ->assertNotFound();
});

it('returns 404 when a freelancer tries to update another freelancer client', function () {
    $freelancer = User::factory()->freelancer()->create();
    $otherFreelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $otherFreelancer->id,
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);

    $this->actingAs($freelancer)
        ->patch(route('clients.update', $client), [
            'name' => 'Hacked Name',
            'email' => 'hacked@example.com',
        ])
        ->assertNotFound();

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);
});

it('allows a freelancer to soft delete their own client', function () {
    $freelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $freelancer->id,
    ]);

    $this->actingAs($freelancer)
        ->delete(route('clients.destroy', $client))
        ->assertRedirect(route('clients.index'));

    $this->assertSoftDeleted('clients', [
        'id' => $client->id,
    ]);
});
