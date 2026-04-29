<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $freelancer = User::factory()->create([
            'name' => 'Demo Freelancer',
            'email' => 'freelancer@example.com',
            'role' => UserRole::Freelancer->value,
        ]);

        $clientUser = User::factory()->create([
            'name' => 'Demo Client',
            'email' => 'client@example.com',
            'role' => UserRole::Client->value,
        ]);

        $client = Client::factory()->create([
            'user_id' => $freelancer->id,
            'client_user_id' => $clientUser->id,
            'name' => 'Demo Client',
            'email' => 'billing@example.com',
        ]);

        $project = Project::factory()->create([
            'user_id' => $freelancer->id,
            'client_id' => $client->id,
            'name' => 'Demo Project',
        ]);

        for ($position = 1; $position <= 2; $position++) {
            $milestone = Milestone::factory()->create([
                'project_id' => $project->id,
                'position' => $position,
            ]);

            Task::factory()
                ->count(3)
                ->create([
                    'milestone_id' => $milestone->id,
                    'assigned_to' => $freelancer->id,
                ])
                ->each(fn (Task $task) => TimeEntry::factory()->create([
                    'user_id' => $freelancer->id,
                    'task_id' => $task->id,
                ]))
            ;
        }
    }
}
