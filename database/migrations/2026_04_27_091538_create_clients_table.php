<?php

declare(strict_types=1);

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
        Schema::create('clients', function (Blueprint $table) {
            $table->comment("Freelancer's client business records — NOT user accounts. See client_user_id for portal link.");

            $table->id();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class, 'client_user_id')->nullable()->constrained()->nullOnDelete()->comment('Set on invitation acceptance. NULL = no portal access yet.');
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('phone', 30)->nullable();
            $table->string('company', 255)->nullable();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // email unique per Freelancer
            $table->unique(['user_id', 'email'], 'clients_user_id_email_unique');
            // plan limit count query optimization
            $table->index(['user_id', 'status', 'deleted_at'], 'clients_user_id_status_deleted_at_index');
            // portal user lookup
            $table->index('client_user_id', 'clients_client_user_id_index');
            $table->index('deleted_at', 'clients_deleted_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
