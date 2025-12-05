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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_code')->unique();
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['pending', 'completed', 'cancelled', 'failed'])->default('pending');
            $table->enum('payment_method', ['momo', 'vietqr', 'card', 'ewallet', 'cod'])->nullable();
            $table->json('items'); // Lưu chi tiết items (tours, hotels, rooms)
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique();
            $table->enum('status', ['pending', 'success', 'failed', 'expired'])->default('pending');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['momo', 'vietqr', 'card', 'ewallet'])->nullable();
            $table->string('request_id')->nullable(); // MoMo requestId
            $table->json('response_data')->nullable(); // Lưu response từ gateway
            $table->text('error_message')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('orders');
    }
};
