<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->comment('Work engagements owned by a Freelancer, assigned to a Client');

            $table->id();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Client::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Invoice::class)->nullable()->constrained()->nullOnDelete();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'active', 'on_hold', 'completed', 'cancelled'])->default('draft');
            $table->date('deadline')->nullable();
            $table
                ->timestamp('last_activity_at')
                ->nullable()
                ->comment('Updated on time_entry creation or task status change — used by stale-check command')
            ;
            $table->timestamps();
            $table->softDeletes();
            $table->index('client_id', 'projects_client_id_index');
            $table->index(['user_id', 'status'], 'projects_user_id_status_index');
            $table->index(['status', 'last_activity_at'], 'projects_status_last_activity_index');
            $table->index('deleted_at', 'projects_deleted_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
