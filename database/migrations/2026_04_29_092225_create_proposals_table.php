<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->comment('Project proposals — one active proposal per project');
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Project::class)->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->longText('content')->comment('HTML/Markdown body');
            $table->enum('status', ['draft', 'sent', 'approved', 'rejected', 'expired'])->default('draft');
            $table->unsignedTinyInteger('expiry_days')->default(14);
            $table->date('expires_at')->nullable()
                ->comment('Computed: sent_at + expiry_days, stored at send time')
            ;
            $table->text('rejection_reason')->nullable()
                ->comment('Required if status=rejected; 10-500 chars')
            ;
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->index('project_id', 'proposals_project_id_index');
            $table->index('user_id', 'proposals_user_id_index');
            $table->index(['status', 'expires_at'], 'proposals_status_expires_at_index');
        });

        DB::statement("
            ALTER TABLE proposals
            ADD active_project_id BIGINT UNSIGNED
            GENERATED ALWAYS AS (
                CASE
                    WHEN status NOT IN ('rejected', 'expired') THEN project_id
                    ELSE NULL
                END
            ) STORED
        ");

        DB::statement('
            CREATE UNIQUE INDEX proposals_project_active_unique
            ON proposals(active_project_id)
        ');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
