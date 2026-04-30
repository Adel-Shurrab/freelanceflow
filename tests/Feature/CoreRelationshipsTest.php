<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DemoDataSeeder::class);
    }

    public function test_freelancer_has_clients_projects_invoices_and_time_entries(): void
    {
        $freelancer = User::query()
            ->where('email', 'freelancer@freelanceflow.test')
            ->firstOrFail()
        ;

        $this->assertCount(1, $freelancer->clients);
        $this->assertCount(1, $freelancer->projects);
        $this->assertCount(1, $freelancer->invoices);
        $this->assertCount(2, $freelancer->timeEntries);
    }

    public function test_client_has_projects_and_portal_user(): void
    {
        $client = Client::query()
            ->where('email', 'client@acme.test')
            ->firstOrFail()
        ;

        $this->assertNotNull($client->portalUser);
        $this->assertSame('client@freelanceflow.test', $client->portalUser->email);

        $this->assertCount(1, $client->projects);
        $this->assertSame('Website Redesign', $client->projects->first()->name);
    }

    public function test_project_has_milestones_tasks_invoice_proposal_and_members(): void
    {
        $project = Project::query()
            ->where('name', 'Website Redesign')
            ->firstOrFail()
        ;

        $this->assertCount(2, $project->milestones);
        $this->assertCount(1, $project->invoices);
        $this->assertCount(1, $project->proposals);
        $this->assertCount(1, $project->members);
        $this->assertNotNull($project->activeProposal);
    }

    public function test_milestone_has_tasks_and_summary(): void
    {
        $milestone = Milestone::query()
            ->where('name', 'Planning')
            ->firstOrFail()
        ;

        $this->assertCount(1, $milestone->tasks);
        $this->assertNotNull($milestone->summary);
        $this->assertSame(1, $milestone->summary->total_tasks);
    }

    public function test_task_has_time_entries(): void
    {
        $task = Task::query()
            ->where('name', 'Collect requirements')
            ->firstOrFail()
        ;

        $this->assertCount(1, $task->timeEntries);
        $this->assertSame(180, $task->timeEntries->first()->duration_minutes);
    }

    public function test_invoice_has_items_payments_and_time_entries(): void
    {
        $invoice = Invoice::query()
            ->where('invoice_number', 'INV-2026-00001')
            ->firstOrFail()
        ;

        $this->assertCount(2, $invoice->items);
        $this->assertCount(1, $invoice->payments);
        $this->assertCount(2, $invoice->timeEntries);

        $this->assertSame('1200.00', $invoice->total_amount);
    }

    public function test_proposal_has_contract_and_comments(): void
    {
        $proposal = Proposal::query()
            ->where('title', 'Website Redesign Proposal')
            ->firstOrFail()
        ;

        $this->assertNotNull($proposal->contract);
        $this->assertCount(1, $proposal->comments);
    }

    public function test_contract_has_two_signatures(): void
    {
        $contract = Contract::query()->firstOrFail();

        $this->assertCount(2, $contract->signatures);
        $this->assertSame(2, $contract->signatureCount());
    }

    public function test_team_and_project_memberships_are_connected(): void
    {
        $agency = User::query()
            ->where('email', 'agency@freelanceflow.test')
            ->firstOrFail()
        ;

        $freelancer = User::query()
            ->where('email', 'freelancer@freelanceflow.test')
            ->firstOrFail()
        ;

        $this->assertCount(1, $agency->teamMembers);
        $this->assertSame($freelancer->id, $agency->teamMembers->first()->id);

        $this->assertCount(1, $freelancer->projectMemberships);
        $this->assertSame('Website Redesign', $freelancer->projectMemberships->first()->name);
    }

    public function test_client_invitation_relationships_work(): void
    {
        $clientUser = User::query()
            ->where('email', 'client@freelanceflow.test')
            ->firstOrFail()
        ;

        $freelancer = User::query()
            ->where('email', 'freelancer@freelanceflow.test')
            ->firstOrFail()
        ;

        $this->assertCount(1, $clientUser->receivedClientInvitations);
        $this->assertCount(1, $freelancer->sentInvitations);

        $invitation = $clientUser->receivedClientInvitations->first();

        $this->assertTrue($invitation->isAccepted());
        $this->assertFalse($invitation->isUsable());
    }
}
