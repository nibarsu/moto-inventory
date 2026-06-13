<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_no', 30)->unique();
            $table->foreignId('sales_order_id')->constrained()->restrictOnDelete();
            $table->date('shipment_date');
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('remark')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_shipments');
    }
};
