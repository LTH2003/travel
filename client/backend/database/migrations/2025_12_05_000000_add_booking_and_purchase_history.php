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
        // Add columns to orders table if not exist
        if (!Schema::hasColumn('orders', 'qr_code')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->longText('qr_code')->nullable()->after('items');
                $table->timestamp('email_sent_at')->nullable()->after('completed_at');
            });
        }

        // Create booking_details table (chi tiết booking)
        if (!Schema::hasTable('booking_details')) {
            Schema::create('booking_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->string('bookable_type'); // 'Tour' hoặc 'Hotel'
                $table->unsignedBigInteger('bookable_id');
                $table->integer('quantity')->default(1);
                $table->decimal('price', 12, 2);
                $table->json('booking_info')->nullable(); // Thông tin khách hàng, ngày đặt, etc
                $table->timestamps();
                
                $table->index(['bookable_type', 'bookable_id']);
            });
        }

        // Create purchase_history table
        if (!Schema::hasTable('purchase_history')) {
            Schema::create('purchase_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->string('item_type'); // 'Tour' hoặc 'Hotel'
                $table->unsignedBigInteger('item_id');
                $table->string('item_name');
                $table->decimal('amount', 12, 2);
                $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
                $table->timestamp('purchased_at');
                $table->timestamps();
                
                $table->index(['user_id', 'item_type']);
                $table->index(['item_type', 'item_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_history');
        Schema::dropIfExists('booking_details');
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['qr_code', 'email_sent_at']);
        });
    }
};
