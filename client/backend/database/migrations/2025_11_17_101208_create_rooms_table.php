<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('size');
            $table->integer('capacity');
            $table->string('beds');
            $table->bigInteger('price');
            $table->bigInteger('original_price')->nullable();
            $table->json('amenities')->nullable();
            $table->json('images')->nullable();
            $table->longText('description')->nullable();
            $table->integer('available')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
