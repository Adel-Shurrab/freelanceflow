<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-2 weeks', '-1 hour');
        $durationMinutes = fake()->numberBetween(30, 240);
        $endedAt = (clone $startedAt)->modify("+{$durationMinutes} minutes");

        return [
            'user_id' => User::factory(),
            'task_id' => Task::factory(),
            'invoice_id' => null,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'duration_minutes' => $durationMinutes,
            'description' => fake()->optional()->sentence(),
            'is_billable' => true,
        ];
    }
}
