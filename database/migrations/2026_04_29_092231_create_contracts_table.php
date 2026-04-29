<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Proposal;
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
        Schema::create('contracts', function (Blueprint $table) {
            $table->comment('Auto-generated contracts from approved proposals');
            $table->id();
            $table->foreignIdFor(Proposal::class)->unique()->constrained()->restrictOnDelete();
            $table->foreignIdFor(Project::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->enum('status', ['draft', 'sent', 'partially_signed', 'fully_signed'])->default('draft');
            $table->longText('content')
                ->comment('Rendered HTML for dompdf. LONGTEXT sufficient for v1')
            ;
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('fully_signed_at')->nullable();
            $table->timestamps();

            $table->index('project_id', 'contracts_project_id_index');
            $table->index('user_id', 'contracts_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
