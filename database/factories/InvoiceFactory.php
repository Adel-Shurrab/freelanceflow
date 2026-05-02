<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'invoice_number' => 'INV-'.now()->format('Y').'-'.fake()->unique()->numberBetween(10000, 99999),
            'status' => InvoiceStatus::Draft,
            'total_amount' => fake()->randomFloat(2, 100, 5000),
            'due_date' => now()->addDays(14)->toDateString(),
            'currency' => 'USD',
            'reminder_count' => 0,
            'notes' => fake()->optional()->sentence(),
            'sent_at' => null,
            'paid_at' => null,
        ];
    }

    public function sent(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::Sent,
            'sent_at' => now(),
        ]);
    }

    public function paid(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::Paid,
            'paid_at' => now(),
        ]);
    }

    public function overdue(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::Overdue,
            'due_date' => now()->subDays(5)->toDateString(),
        ]);
    }

    public function cancelled(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::Cancelled,
        ]);
    }
}
