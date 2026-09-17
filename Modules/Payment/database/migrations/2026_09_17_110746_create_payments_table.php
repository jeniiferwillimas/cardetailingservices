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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('order_reference')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('usd');
            $table->enum('status', [
                'pending', 'waiting', 'confirming', 'confirmed', 'sending', 'finished', 'failed', 'expired',
            ])->default('pending');
            $table->string('provider')->default('nowpayments');
            $table->string('provider_invoice_id')->nullable();
            $table->string('provider_payment_id')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
