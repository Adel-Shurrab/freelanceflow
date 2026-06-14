<?php

declare(strict_types=1);

use App\Enums\ClientStatus;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows a freelancer to view only their own clients', function () {
    $freelancer = User::factory()->freelancer()->create();
    $otherFreelancer = User::factory()->freelancer()->create();

    Client::factory()->create([
        'user_id' => $freelancer->id,
        'name' => 'Own Client',
        'email' => 'own@example.com',
    ]);

    Client::factory()->create([
        'user_id' => $otherFreelancer->id,
        'name' => 'Other Client',
        'email' => 'other@example.com',
    ]);

    $this->actingAs($freelancer)
        ->get(route('clients.index'))
        ->assertOk()
        ->assertSee('Own Client')
        ->assertDontSee('Other Client')
    ;
});

it('allows a freelancer to search clients by name or email', function () {
    $freelancer = User::factory()->freelancer()->create();

    Client::factory()->create([
        'user_id' => $freelancer->id,
        'name' => 'Acme Corp',
        'email' => 'contact@acme.test',
    ]);

    Client::factory()->create([
        'user_id' => $freelancer->id,
        'name' => 'Beta Studio',
        'email' => 'hello@beta.test',
    ]);

    $this->actingAs($freelancer)
        ->get(route('clients.index', ['search' => 'Acme']))
        ->assertOk()
        ->assertSee('Acme Corp')
        ->assertDontSee('Beta Studio')
    ;

    $this->actingAs($freelancer)
        ->get(route('clients.index', ['search' => 'hello@beta.test']))
        ->assertOk()
        ->assertSee('Beta Studio')
        ->assertDontSee('Acme Corp')
    ;
});

it('allows a freelancer to filter clients by status', function () {
    $freelancer = User::factory()->freelancer()->create();

    Client::factory()->create([
        'user_id' => $freelancer->id,
        'name' => 'Active Client',
        'status' => ClientStatus::Active,
    ]);

    Client::factory()->create([
        'user_id' => $freelancer->id,
        'name' => 'Archived Client',
        'status' => ClientStatus::Archived,
    ]);

    $this->actingAs($freelancer)
        ->get(route('clients.index', ['status' => ClientStatus::Active->value]))
        ->assertOk()
        ->assertSee('Active Client')
        ->assertDontSee('Archived Client')
    ;

    $this->actingAs($freelancer)
        ->get(route('clients.index', ['status' => ClientStatus::Archived->value]))
        ->assertOk()
        ->assertSee('Archived Client')
        ->assertDontSee('Active Client')
    ;
});

it('rejects invalid client status filters', function () {
    $freelancer = User::factory()->freelancer()->create();

    $this->actingAs($freelancer)
        ->get(route('clients.index', ['status' => 'invalid-status']))
        ->assertSessionHasErrors('status')
    ;
});
