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
        if (!Schema::hasTable('otp_codes')) {
            Schema::create('otp_codes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('code', 6); // 6-digit OTP code
                $table->timestamp('expires_at');
                $table->boolean('used')->default(false);
                $table->timestamps();

                // Foreign key
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                
                // Indexes for performance
                $table->index(['user_id', 'used']);
                $table->index('expires_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
