<?php

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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_code')->unique();
            $table->date('transaction_date');
            $table->enum('transaction_status', ['pending', 'paid', 'unpaid'])->default('pending');
            $table->decimal('total_amount', 10, 2);
            $table->foreignId('customer_id');
            $table->text('shipping_address')->nullable();
            $table->string('payment_method')->nullable();
            $table->foreignId('discount_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
