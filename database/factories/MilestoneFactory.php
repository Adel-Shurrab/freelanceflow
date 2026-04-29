<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MilestoneStatus;
use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Milestone>
 */
class MilestoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->words(2, true),
            'description' => fake()->optional()->paragraph(),
            'status' => MilestoneStatus::Pending->value,
            'position' => fake()->numberBetween(1, 5),
            'due_date' => fake()->dateTimeBetween('+1 week', '+4 months')->format('Y-m-d'),
        ];
    }
}
