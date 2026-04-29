<?php

declare(strict_types=1);

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->comment('Payment records — Freelancer manual (POST /payments) or Client mock simulation (POST /pay)');
            $table->id();
            $table->foreignIdFor(Invoice::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class, 'recorded_by')->nullable()->constrained()->nullOnDelete();

            $table->decimal('amount', 10, 2)
                ->comment('Cannot exceed outstanding balance')
            ;
            $table->char('currency', 3)->default('USD');
            $table->string('payment_method', 100)->nullable()
                ->comment('bank_transfer|mock_simulation|cash|cheque. VARCHAR for flexibility.')
            ;
            $table->string('payment_reference', 255)->nullable()
                ->comment('SENSITIVE — not logged in activity_log')
            ;
            $table->text('notes')->nullable();
            // nullable — set explicitly when payment confirmed, NOT auto-set on INSERT
            $table->timestamp('paid_at')->nullable()
                ->comment('Set explicitly when payment is confirmed')
            ;
            $table->timestamps();

            $table->index('invoice_id', 'payments_invoice_id_index');
            $table->index('recorded_by', 'payments_recorded_by_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
