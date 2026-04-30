<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_invitations', function (Blueprint $table) {
            $table->comment('Tracks 48-hour signed invitation links for client onboarding');
            $table->id();
            $table->foreignIdFor(User::class, 'client_user_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'invited_by')->constrained()->restrictOnDelete();
            $table->string('token', 128)->unique();
            $table->timestamp('expires_at')->comment('48 hours from created_at');
            $table->timestamp('accepted_at')->nullable()->comment('Set on first password creation');
            $table->timestamp('created_at')->useCurrent();

            $table->index('client_user_id', 'client_invitations_client_user_id_index');
            $table->index('invited_by', 'client_invitations_invited_by_index');
            $table->index('expires_at', 'client_invitations_expires_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_invitations');
    }
};
