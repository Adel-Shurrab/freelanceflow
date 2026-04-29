<?php

declare(strict_types=1);

use App\Models\Milestone;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->comment('Atomic work items within a Milestone');
            $table->foreignIdFor(Milestone::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'assigned_to')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->enum('status', ['to_do', 'in_progress', 'in_review', 'done'])->default('to_do');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->date('due_date')->nullable()->comment('Cannot be after milestone due date');
            $table->timestamps();

            $table->index('milestone_id', 'tasks_milestone_id_index');
            $table->index('assigned_to', 'tasks_assigned_to_index');
            $table->index(['milestone_id', 'status'], 'tasks_milestone_id_status_index');
            $table->index('updated_at', 'tasks_updated_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
