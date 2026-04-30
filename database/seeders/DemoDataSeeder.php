<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ClientStatus;
use App\Enums\ContractStatus;
use App\Enums\InvoiceStatus;
use App\Enums\MilestoneStatus;
use App\Enums\PlanType;
use App\Enums\ProjectStatus;
use App\Enums\ProposalStatus;
use App\Enums\SignatureType;
use App\Enums\SignerType;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Client;
use App\Models\ClientInvitation;
use App\Models\Comment;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Milestone;
use App\Models\MilestoneSummary;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Proposal;
use App\Models\Signature;
use App\Models\Subscription;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = $this->createUsers();
        ['freelancer' => $freelancer, 'agencyAdmin' => $agencyAdmin, 'clientUser' => $clientUser] = $superAdmin;

        $this->createTeamAndSubscription($agencyAdmin, $freelancer);
        $client = $this->createClient($freelancer, $clientUser);
        ClientInvitation::query()->create([
            'client_user_id' => $clientUser->id,
            'invited_by' => $freelancer->id,
            'token' => Str::random(64),
            'expires_at' => now()->addHours(48),
            'accepted_at' => now()->subDay(),
            'created_at' => now()->subDays(2),
        ]);

        $project = $this->createProject($freelancer, $agencyAdmin, $client);
        ['planningMilestone' => $planningMilestone, 'developmentMilestone' => $developmentMilestone] = $this->createMilestones($project);
        ['taskOne' => $taskOne, 'taskTwo' => $taskTwo] = $this->createTasks($planningMilestone, $developmentMilestone, $freelancer);

        $this->createTimeEntries($freelancer, $taskOne, $taskTwo);
        MilestoneSummary::query()->create([
            'milestone_id' => $planningMilestone->id,
            'completed_at' => now()->subDay(),
            'total_tasks' => 1,
            'total_hours' => 3.00,
            'created_at' => now(),
        ]);

        $proposal = $this->createProposal($project, $freelancer);
        $contract = $this->createContract($proposal, $project, $freelancer);
        $this->createSignatures($contract, $freelancer, $clientUser);

        $invoice = $this->createInvoice($freelancer, $project);
        $this->createInvoiceItems($invoice);
        $this->createPayment($invoice, $freelancer);

        TimeEntry::query()
            ->where('user_id', $freelancer->id)
            ->whereNull('invoice_id')
            ->update(['invoice_id' => $invoice->id])
        ;

        $this->createComments($project, $proposal, $freelancer, $clientUser);
    }

    private function createUsers(): array
    {
        $superAdmin = User::query()->updateOrCreate(
            ['email' => 'superadmin@freelanceflow.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::SuperAdmin,
                'plan_type' => PlanType::Agency,
                'email_verified_at' => now(),
                'timezone' => 'Asia/Gaza',
                'language' => 'en',
            ],
        );

        $agencyAdmin = User::query()->updateOrCreate(
            ['email' => 'agency@freelanceflow.test'],
            [
                'name' => 'Agency Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'plan_type' => PlanType::Agency,
                'email_verified_at' => now(),
                'timezone' => 'Asia/Gaza',
                'language' => 'en',
            ],
        );

        $freelancer = User::query()->updateOrCreate(
            ['email' => 'freelancer@freelanceflow.test'],
            [
                'name' => 'Adel Freelancer',
                'password' => Hash::make('password'),
                'role' => UserRole::Freelancer,
                'plan_type' => PlanType::Pro,
                'plan_started_at' => now()->subMonth(),
                'plan_expires_at' => now()->addMonths(11),
                'email_verified_at' => now(),
                'timezone' => 'Asia/Gaza',
                'language' => 'en',
            ],
        );

        $clientUser = User::query()->updateOrCreate(
            ['email' => 'client@freelanceflow.test'],
            [
                'name' => 'Client Portal User',
                'password' => Hash::make('password'),
                'role' => UserRole::Client,
                'plan_type' => PlanType::Free,
                'email_verified_at' => now(),
                'timezone' => 'Asia/Gaza',
                'language' => 'en',
            ],
        );

        return ['superAdmin' => $superAdmin, 'agencyAdmin' => $agencyAdmin, 'freelancer' => $freelancer, 'clientUser' => $clientUser];
    }

    private function createTeamAndSubscription(User $agencyAdmin, User $freelancer): void
    {
        TeamMember::query()->updateOrCreate(
            [
                'agency_user_id' => $agencyAdmin->id,
                'member_user_id' => $freelancer->id,
            ],
            [
                'created_at' => now(),
            ],
        );

        Subscription::query()->create([
            'user_id' => $freelancer->id,
            'plan_type' => PlanType::Pro,
            'started_at' => now()->subMonth(),
            'expires_at' => now()->addMonths(11),
            'upgraded_from' => PlanType::Free,
            'payment_reference' => 'MOCK-'.Str::uuid(),
            'amount_paid' => 29.00,
            'currency' => 'USD',
        ]);
    }

    private function createClient(User $freelancer, User $clientUser): Client
    {
        return Client::query()->updateOrCreate(
            [
                'user_id' => $freelancer->id,
                'email' => 'client@acme.test',
            ],
            [
                'client_user_id' => $clientUser->id,
                'name' => 'Acme Client',
                'phone' => '+970599000000',
                'company' => 'Acme Studio',
                'status' => ClientStatus::Active,
                'notes' => 'Demo client used for local development.',
            ],
        );
    }

    private function createProject(User $freelancer, User $agencyAdmin, Client $client): Project
    {
        $project = Project::query()->create([
            'user_id' => $freelancer->id,
            'client_id' => $client->id,
            'name' => 'Website Redesign',
            'description' => 'Full redesign and Laravel implementation for client portal.',
            'status' => ProjectStatus::Active,
            'deadline' => now()->addMonth()->toDateString(),
            'last_activity_at' => now(),
        ]);

        ProjectMember::query()->create([
            'project_id' => $project->id,
            'user_id' => $freelancer->id,
            'assigned_at' => now(),
            'assigned_by' => $agencyAdmin->id,
        ]);

        return $project;
    }

    private function createMilestones(Project $project): array
    {
        $planningMilestone = Milestone::query()->create([
            'project_id' => $project->id,
            'name' => 'Planning',
            'description' => 'Requirements, scope, and wireframes.',
            'status' => MilestoneStatus::Completed,
            'position' => 1,
            'due_date' => now()->addWeek()->toDateString(),
        ]);

        $developmentMilestone = Milestone::query()->create([
            'project_id' => $project->id,
            'name' => 'Development',
            'description' => 'Backend and frontend implementation.',
            'status' => MilestoneStatus::InProgress,
            'position' => 2,
            'due_date' => now()->addWeeks(3)->toDateString(),
        ]);

        return ['planningMilestone' => $planningMilestone, 'developmentMilestone' => $developmentMilestone];
    }

    private function createTasks(Milestone $planningMilestone, Milestone $developmentMilestone, User $freelancer): array
    {
        $taskOne = Task::query()->create([
            'milestone_id' => $planningMilestone->id,
            'assigned_to' => $freelancer->id,
            'name' => 'Collect requirements',
            'description' => 'Meet with client and define project scope.',
            'status' => TaskStatus::Done,
            'priority' => TaskPriority::High,
            'due_date' => now()->addDays(3)->toDateString(),
        ]);

        $taskTwo = Task::query()->create([
            'milestone_id' => $developmentMilestone->id,
            'assigned_to' => $freelancer->id,
            'name' => 'Build client dashboard',
            'description' => 'Implement the first dashboard version.',
            'status' => TaskStatus::InProgress,
            'priority' => TaskPriority::Medium,
            'due_date' => now()->addWeeks(2)->toDateString(),
        ]);

        return ['taskOne' => $taskOne, 'taskTwo' => $taskTwo];
    }

    private function createTimeEntries(User $freelancer, Task $taskOne, Task $taskTwo): void
    {
        TimeEntry::query()->create([
            'user_id' => $freelancer->id,
            'task_id' => $taskOne->id,
            'invoice_id' => null,
            'started_at' => now()->subDays(3)->setTime(9, 0),
            'ended_at' => now()->subDays(3)->setTime(12, 0),
            'duration_minutes' => 180,
            'description' => 'Requirements session and project notes.',
            'is_billable' => true,
        ]);

        TimeEntry::query()->create([
            'user_id' => $freelancer->id,
            'task_id' => $taskTwo->id,
            'invoice_id' => null,
            'started_at' => now()->subDay()->setTime(10, 0),
            'ended_at' => now()->subDay()->setTime(14, 30),
            'duration_minutes' => 270,
            'description' => 'Initial dashboard implementation.',
            'is_billable' => true,
        ]);
    }

    private function createProposal(Project $project, User $freelancer): Proposal
    {
        return Proposal::query()->create([
            'project_id' => $project->id,
            'user_id' => $freelancer->id,
            'title' => 'Website Redesign Proposal',
            'content' => '<p>Proposal for redesigning and building the client portal.</p>',
            'status' => ProposalStatus::Approved,
            'expiry_days' => 14,
            'expires_at' => now()->addDays(14)->toDateString(),
            'sent_at' => now()->subDays(5),
            'approved_at' => now()->subDays(3),
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);
    }

    private function createContract(Proposal $proposal, Project $project, User $freelancer): Contract
    {
        return Contract::query()->create([
            'proposal_id' => $proposal->id,
            'project_id' => $project->id,
            'user_id' => $freelancer->id,
            'status' => ContractStatus::FullySigned,
            'content' => '<p>Contract terms for Website Redesign project.</p>',
            'sent_at' => now()->subDays(3),
            'fully_signed_at' => now()->subDays(2),
        ]);
    }

    private function createSignatures(Contract $contract, User $freelancer, User $clientUser): void
    {
        Signature::query()->create([
            'signable_type' => Contract::class,
            'signable_id' => $contract->id,
            'signer_type' => SignerType::Freelancer,
            'signer_id' => $freelancer->id,
            'signature_type' => SignatureType::TypedName,
            'signature_data' => $freelancer->name,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Demo Seeder',
            'signed_at' => now()->subDays(2),
        ]);

        Signature::query()->create([
            'signable_type' => Contract::class,
            'signable_id' => $contract->id,
            'signer_type' => SignerType::Client,
            'signer_id' => $clientUser->id,
            'signature_type' => SignatureType::TypedName,
            'signature_data' => $clientUser->name,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Demo Seeder',
            'signed_at' => now()->subDays(2),
        ]);
    }

    private function createInvoice(User $freelancer, Project $project): Invoice
    {
        return Invoice::query()->updateOrCreate(
            ['invoice_number' => 'INV-2026-00001'],
            [
                'user_id' => $freelancer->id,
                'project_id' => $project->id,
                'status' => InvoiceStatus::Paid,
                'total_amount' => 1200.00,
                'due_date' => now()->addDays(10)->toDateString(),
                'currency' => 'USD',
                'reminder_count' => 0,
                'notes' => 'Demo paid invoice.',
                'sent_at' => now()->subDays(4),
                'paid_at' => now()->subDay(),
            ],
        );
    }

    private function createInvoiceItems(Invoice $invoice): void
    {
        InvoiceItem::query()->updateOrCreate(
            [
                'invoice_id' => $invoice->id,
                'description' => 'Planning and requirements',
            ],
            [
                'quantity' => 3.00,
                'unit_price' => 100.00,
                'subtotal' => 300.00,
            ],
        );

        InvoiceItem::query()->updateOrCreate(
            [
                'invoice_id' => $invoice->id,
                'description' => 'Dashboard implementation',
            ],
            [
                'quantity' => 9.00,
                'unit_price' => 100.00,
                'subtotal' => 900.00,
            ],
        );
    }

    private function createPayment(Invoice $invoice, User $freelancer): void
    {
        Payment::query()->updateOrCreate(
            [
                'invoice_id' => $invoice->id,
                'payment_reference' => 'MOCK-PAY-INV-2026-00001',
            ],
            [
                'recorded_by' => $freelancer->id,
                'amount' => 1200.00,
                'currency' => 'USD',
                'payment_method' => 'mock_simulation',
                'notes' => 'Demo full payment.',
                'paid_at' => now()->subDay(),
            ],
        );
    }

    private function createComments(Project $project, Proposal $proposal, User $freelancer, User $clientUser): void
    {
        Comment::query()->create([
            'commentable_type' => Project::class,
            'commentable_id' => $project->id,
            'user_id' => $freelancer->id,
            'body' => 'Project kickoff completed. Moving to development phase.',
        ]);

        Comment::query()->create([
            'commentable_type' => Proposal::class,
            'commentable_id' => $proposal->id,
            'user_id' => $clientUser->id,
            'body' => 'Proposal approved. Ready to proceed.',
        ]);
    }
}
