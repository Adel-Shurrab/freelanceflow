<?php

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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->comment('Audit/history only — plan_type on users table is the enforcement column');
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->enum('plan_type', ['free', 'pro', 'agency']);
            $table->timestamp('started_at');
            $table->timestamp('expires_at')->nullable()->comment('NULL for Free plan — never expires');
            $table->enum('upgraded_from', ['free', 'pro', 'agency'])->nullable()->comment('NULL for Free plan — never upgraded');
            $table->string('payment_reference', 100)->nullable()->comment('Format: MOCK-{uuid}. SENSITIVE — not logged in activity_log');
            $table->decimal('amount_paid', 8, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->timestamps();

            $table->index('user_id', 'subscriptions_user_id_index');
            $table->index(['user_id', 'plan_type'], 'subscriptions_user_plan_index');
            $table->index('expires_at', 'subscriptions_expires_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
