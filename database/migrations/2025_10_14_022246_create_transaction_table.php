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
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->string('trx_id')->unique();
            $table->string('payment_request_id')->nullable();
            $table->string('va_number')->nullable();
            $table->string('customer_no')->nullable();
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('currency', 5)->default('IDR');
            $table->string('status', 20)->default('PENDING');
            $table->timestamp('trx_datetime')->nullable();
            $table->timestamp('paid_at')->nullable();

            // 🔹 Detail tambahan dari "additionalInfo"
            $table->string('member_code')->nullable();
            $table->string('debit_from')->nullable();
            $table->string('debit_from_name')->nullable();
            $table->string('debit_from_bank')->nullable();
            $table->string('credit_to')->nullable();
            $table->string('credit_to_name')->nullable();
            $table->string('credit_to_bank')->nullable();
            $table->string('product_code')->nullable();
            $table->string('product_value')->nullable();
            $table->string('fee_type')->nullable();
            $table->decimal('tx_fee', 15, 2)->nullable();
            $table->string('payment_ref')->nullable();
            $table->string('user_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
