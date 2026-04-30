<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_members', function (Blueprint $table) {
            $table->comment('Team assignment to projects — Agency plan only');
            $table->id();
            $table->foreignIdFor(Project::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->foreignIdFor(User::class, 'assigned_by')->nullable()->constrained()->nullOnDelete()->comment('Who assigned the team member');

            $table->unique(['project_id', 'user_id'], 'project_members_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};
