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
        Schema::create('signatures', function (Blueprint $table) {
            $table->comment('Polymorphic typed-name signatures on contracts');
            $table->id();
            $table->morphs('signable');
            $table->string('signer_type', 50)->comment('freelancer|client');
            $table->foreignIdFor(User::class, 'signer_id')->constrained()->restrictOnDelete();
            $table->enum('signature_type', ['typed_name'])->default('typed_name');
            $table->text('signature_data')->comment('Typed full name');;
            $table->string('ip_address', 45);
            $table->string('user_agent', 512);
            $table->dateTime('signed_at');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(
                ['signable_type', 'signable_id', 'signer_type'],
                'signatures_signable_signer_unique',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
