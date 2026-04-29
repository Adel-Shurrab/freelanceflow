<?php

declare(strict_types=1);

use App\Models\Project;
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
        Schema::create('milestones', function (Blueprint $table) {
            $table->comment('Project phases — ordered by position, auto-completed by TaskObserver');
            $table->id();
            $table->foreignIdFor(Project::class)->constrained()->restrictOnDelete();
            $table->string('name', 255);
            $table->string('description', 255)->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->smallInteger('position')->unsigned()->default(0)->comment('Display order within project');
            $table->date('due_date')->nullable()->comment('Cannot be after project.deadline');
            $table->timestamps();

            // Ordered milestone list: ORDER BY position
            $table->index(['project_id', 'position'], 'milestones_project_id_position_index');
            // TaskObserver: check all tasks done for milestone completion
            $table->index(['project_id', 'status'], 'milestones_project_id_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
