<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows a freelancer to soft delete their own client', function () {
    $freelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $freelancer->id,
    ]);

    $this->actingAs($freelancer)
        ->delete(route('clients.destroy', $client))
        ->assertRedirect(route('clients.index'))
    ;

    $this->assertSoftDeleted('clients', [
        'id' => $client->id,
    ]);
});

it('prevents deleting a client with projects', function () {
    $freelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $freelancer->id,
    ]);

    Project::factory()->create([
        'user_id' => $freelancer->id,
        'client_id' => $client->id,
    ]);

    $this->actingAs($freelancer)
        ->from(route('clients.show', $client))
        ->delete(route('clients.destroy', $client))
        ->assertRedirect(route('clients.show', $client))
        ->assertSessionHasErrors('client')
    ;

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'deleted_at' => null,
    ]);
});
