<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Milestone;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'milestone_id' => Milestone::factory(),
            'assigned_to' => User::factory(),
            'name' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => TaskStatus::ToDo->value,
            'priority' => fake()->randomElement([
                TaskPriority::Low->value,
                TaskPriority::Medium->value,
                TaskPriority::High->value,
            ]),
            'due_date' => fake()->dateTimeBetween('+1 day', '+2 months')->format('Y-m-d'),
        ];
    }
}
