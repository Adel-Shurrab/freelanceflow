<?php

declare(strict_types=1);

use App\Enums\ClientStatus;
use App\Enums\ProjectStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows a freelancer to archive their own client', function () {
    $freelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $freelancer->id,
        'status' => ClientStatus::Active,
    ]);

    $this->actingAs($freelancer)
        ->patch(route('clients.archive', $client))
        ->assertRedirect(route('clients.show', $client))
    ;

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'status' => ClientStatus::Archived->value,
    ]);
});

it('moves active projects to on hold when archiving a client', function () {
    $freelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $freelancer->id,
        'status' => ClientStatus::Active,
    ]);

    $activeProject = Project::factory()->create([
        'user_id' => $freelancer->id,
        'client_id' => $client->id,
        'status' => ProjectStatus::Active,
    ]);

    $completedProject = Project::factory()->create([
        'user_id' => $freelancer->id,
        'client_id' => $client->id,
        'status' => ProjectStatus::Completed,
    ]);

    $this->actingAs($freelancer)
        ->patch(route('clients.archive', $client))
        ->assertRedirect(route('clients.show', $client))
    ;

    $this->assertDatabaseHas('projects', [
        'id' => $activeProject->id,
        'status' => ProjectStatus::OnHold->value,
    ]);

    $this->assertDatabaseHas('projects', [
        'id' => $completedProject->id,
        'status' => ProjectStatus::Completed->value,
    ]);
});

it('prevents archiving a client with unpaid invoices', function () {
    $freelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $freelancer->id,
        'status' => ClientStatus::Active,
    ]);

    $project = Project::factory()->create([
        'user_id' => $freelancer->id,
        'client_id' => $client->id,
    ]);

    Invoice::factory()->sent()->create([
        'user_id' => $freelancer->id,
        'project_id' => $project->id,
    ]);

    $this->actingAs($freelancer)
        ->patch(route('clients.archive', $client))
        ->assertSessionHasErrors('client')
    ;

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'status' => ClientStatus::Active->value,
    ]);
});

it('allows a freelancer to restore their archived client', function () {
    $freelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $freelancer->id,
        'status' => ClientStatus::Archived,
    ]);

    $this->actingAs($freelancer)
        ->patch(route('clients.restore', $client))
        ->assertRedirect(route('clients.show', $client))
    ;

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'status' => ClientStatus::Active->value,
    ]);
});

it('prevents restoring an archived client if active limit reached', function () {
    $freelancer = User::factory()->freelancer()->create();

    Client::factory()
        ->count(3)
        ->create([
            'user_id' => $freelancer->id,
            'status' => ClientStatus::Active,
        ])
    ;

    $archivedClient = Client::factory()->create([
        'user_id' => $freelancer->id,
        'status' => ClientStatus::Archived,
    ]);

    $this->actingAs($freelancer)
        ->patch(route('clients.restore', $archivedClient))
        ->assertSessionHasErrors('plan')
    ;

    $this->assertDatabaseHas('clients', [
        'id' => $archivedClient->id,
        'status' => ClientStatus::Archived->value,
    ]);
});

it('returns 404 when a freelancer tries to archive another freelancer client', function () {
    $freelancer = User::factory()->freelancer()->create();
    $otherFreelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $otherFreelancer->id,
    ]);

    $this->actingAs($freelancer)
        ->patch(route('clients.archive', $client))
        ->assertNotFound()
    ;
});

it('returns 404 when a freelancer tries to restore another freelancer client', function () {
    $freelancer = User::factory()->freelancer()->create();
    $otherFreelancer = User::factory()->freelancer()->create();

    $client = Client::factory()->create([
        'user_id' => $otherFreelancer->id,
        'status' => ClientStatus::Archived,
    ]);

    $this->actingAs($freelancer)
        ->patch(route('clients.restore', $client))
        ->assertNotFound();
});
