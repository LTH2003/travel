<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->double('rating', 2, 1)->default(0);
            $table->bigInteger('price');
            $table->bigInteger('original_price')->nullable();
            $table->text('image')->nullable(); // ảnh đại diện trong danh sách
            $table->json('images')->nullable(); // ảnh lớn trong trang detail
            $table->json('amenities')->nullable();
            $table->longText('description')->nullable();
            $table->integer('reviews')->default(0);
            
            // Policies
            $table->string('check_in')->nullable();
            $table->string('check_out')->nullable();
            $table->string('cancellation')->nullable();
            $table->string('children')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
