<?php

declare(strict_types=1);

use App\Enums\ClientStatus;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows a freelancer to create a client', function () {
    $freelancer = User::factory()->freelancer()->create();

    $response = $this->actingAs($freelancer)
        ->post(route('clients.store'), [
            'name' => 'Acme Corp',
            'email' => 'client@acme.test',
            'phone' => '+970599000000',
            'company' => 'Acme',
            'notes' => 'Important client.',
        ])
    ;

    $client = Client::query()
        ->where('email', 'client@acme.test')
        ->firstOrFail()
    ;

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
        ])
    ;
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
        ->assertRedirect()
    ;

    $this->assertDatabaseHas('clients', [
        'user_id' => $freelancer->id,
        'email' => 'shared@example.com',
    ]);

    $this->assertDatabaseHas('clients', [
        'user_id' => $otherFreelancer->id,
        'email' => 'shared@example.com',
    ]);
});

it('prevents free freelancers from creating more than three active clients', function () {
    $freelancer = User::factory()->freelancer()->create();

    Client::factory()
        ->count(3)
        ->create([
            'user_id' => $freelancer->id,
        ])
    ;

    $this->actingAs($freelancer)
        ->from(route('clients.create'))
        ->post(route('clients.store'), [
            'name' => 'Fourth Client',
            'email' => 'fourth@example.com',
        ])
        ->assertRedirect(route('clients.create'))
        ->assertSessionHasErrors('plan')
    ;

    $this->assertDatabaseMissing('clients', [
        'user_id' => $freelancer->id,
        'email' => 'fourth@example.com',
    ]);
});

it('allows pro freelancers to create more than three clients', function () {
    $freelancer = User::factory()->proFreelancer()->create();

    Client::factory()
        ->count(3)
        ->create([
            'user_id' => $freelancer->id,
        ])
    ;

    $this->actingAs($freelancer)
        ->post(route('clients.store'), [
            'name' => 'Fourth Client',
            'email' => 'fourth@example.com',
        ])
        ->assertRedirect()
    ;

    $this->assertDatabaseHas('clients', [
        'user_id' => $freelancer->id,
        'email' => 'fourth@example.com',
    ]);
});

it('does not count archived clients against the free client limit', function () {
    $freelancer = User::factory()->freelancer()->create();

    Client::factory()
        ->count(2)
        ->create([
            'user_id' => $freelancer->id,
            'status' => ClientStatus::Active,
        ])
    ;

    Client::factory()->create([
        'user_id' => $freelancer->id,
        'status' => ClientStatus::Archived,
    ]);

    $this->actingAs($freelancer)
        ->post(route('clients.store'), [
            'name' => 'Allowed Client',
            'email' => 'allowed@example.com',
        ])
        ->assertRedirect()
    ;

    $this->assertDatabaseHas('clients', [
        'user_id' => $freelancer->id,
        'email' => 'allowed@example.com',
    ]);
});

it('does not count soft deleted clients against the free client limit', function () {
    $freelancer = User::factory()->freelancer()->create();

    Client::factory()
        ->count(3)
        ->create([
            'user_id' => $freelancer->id,
        ])
    ;

    Client::query()
        ->where('user_id', $freelancer->id)
        ->firstOrFail()
        ->delete()
    ;

    $this->actingAs($freelancer)
        ->post(route('clients.store'), [
            'name' => 'Replacement Client',
            'email' => 'replacement@example.com',
        ])
        ->assertRedirect()
    ;

    $this->assertDatabaseHas('clients', [
        'user_id' => $freelancer->id,
        'email' => 'replacement@example.com',
    ]);
});
