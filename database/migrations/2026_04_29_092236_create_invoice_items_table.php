<?php

declare(strict_types=1);

use App\Models\Invoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->comment('Invoice line items — max 50 per invoice, no tax in v1');
            $table->id();
            $table->foreignIdFor(Invoice::class)->constrained()->cascadeOnDelete();
            $table->string('description', 500);
            $table->decimal('quantity', 8, 2)
                ->comment('min 0.01')
            ;
            $table->decimal('unit_price', 10, 2)
                ->comment('min 0')
            ;
            $table->decimal('subtotal', 10, 2)
                ->comment('Application-managed: quantity * unit_price. Computed in InvoiceItem::saving().')
            ;
            $table->timestamps();

            $table->index('invoice_id', 'invoice_items_invoice_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
