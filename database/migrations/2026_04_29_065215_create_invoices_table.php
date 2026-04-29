<?php

use App\Models\Project;
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
        Schema::create('invoices', function (Blueprint $table) {
            $table->comment('Billing documents — creation requires approved proposal');
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Project::class)->constrained()->restrictOnDelete();
            $table->string('invoice_number')->unique()
                ->comment('Format: INV-YYYY-00001. Set by InvoiceObserver on creation.')
            ;
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->decimal('total_amount', 10, 2)->default(0.00)
                ->comment('Stored; recomputed by InvoiceObserver on item changes.')
            ;
            $table->date('due_date')->nullable()
                ->comment('Must be >= today on creation')
            ;
            $table->char('currency', 3)->default('USD')
                ->comment('ISO 4217 currency code')
            ;
            $table->unsignedTinyInteger('reminder_count')->default(0)
                ->comment('Max 3 overdue reminders')
            ;
            $table->text('notes')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('user_id', 'invoices_user_id_index');
            $table->index('project_id', 'invoices_project_id_index');
            // Dashboard: unpaid invoices count; filter by status
            $table->index(['user_id', 'status'], 'invoices_user_id_status_index');
            // Daily overdue command: WHERE status='sent' AND due_date < NOW()
            $table->index(['status', 'due_date'], 'invoices_status_due_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
