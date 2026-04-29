<?php

use App\Models\Task;
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
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->comment('Hours logged against Tasks — read-only once invoice is Sent/Overdue/Paid');
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Task::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Invoice::class)->nullable()->constrained()->nullOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->comment('Must be strictly after started_at; max 24h range');
            $table->unsignedMediumInteger('duration_minutes')->default(0)
                ->comment('Application-managed: TIMESTAMPDIFF(MINUTE, started_at, ended_at)');
            $table->string('description', 1000)->nullable();
            $table->boolean('is_billable')->default(true);
            $table->timestamps();

            $table->index('user_id', 'te_user_id_index');
            $table->index('task_id', 'te_task_id_index');
            $table->index('invoice_id', 'te_invoice_id_index');

            $table->index(['user_id', 'task_id', 'started_at'], 'te_user_task_started_index');
            $table->index(['user_id', 'started_at'], 'te_user_started_at_index');
            $table->index(['task_id', 'created_at'], 'te_task_id_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
