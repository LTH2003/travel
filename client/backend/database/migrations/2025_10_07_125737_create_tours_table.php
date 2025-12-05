<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('destination');
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->string('duration');
            $table->decimal('rating', 2, 1);
            $table->integer('review_count');
            $table->string('image');
            $table->string('category');
            $table->json('highlights');
            $table->text('description');
            $table->json('includes');
            $table->json('itinerary');
            $table->json('departure');
            $table->integer('max_guests');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
