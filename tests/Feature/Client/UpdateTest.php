<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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
        ->assertNotFound()
    ;

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);
});

it('allows a freelancer to update their own client', function () {
    $freelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $freelancer->id,
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $this->actingAs($freelancer)
        ->patch(route('clients.update', $client), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '+970599111111',
            'company' => 'Updated Company',
            'notes' => 'Updated notes.',
        ])
        ->assertRedirect(route('clients.show', $client))
    ;

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'phone' => '+970599111111',
        'company' => 'Updated Company',
        'notes' => 'Updated notes.',
    ]);
});
