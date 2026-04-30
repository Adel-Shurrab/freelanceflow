<?php

declare(strict_types=1);

use App\Models\Milestone;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestone_summaries', function (Blueprint $table) {
            $table->comment('Denormalised completion stats — generated on milestone completion');
            $table->id();
            $table->foreignIdFor(Milestone::class)->unique()->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at');
            $table->unsignedInteger('total_tasks')->default(0)->comment('Count at completion time');
            $table->decimal('total_hours', 8, 2)->default(0.00)->comment('Sum of time_entries duration_minutes / 60 for all milestone tasks');
            $table->timestamp('created_at')->useCurrent();

            $table->index('completed_at', 'milestone_summaries_completed_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone_summaries');
    }
};
