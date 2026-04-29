<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->paragraph(),
            'status' => ProjectStatus::Active->value,
            'deadline' => fake()->dateTimeBetween('+2 weeks', '+6 months')->format('Y-m-d'),
            'last_activity_at' => now(),
        ];
    }
}
