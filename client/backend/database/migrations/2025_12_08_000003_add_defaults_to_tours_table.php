<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            // Add default values to required columns
            $table->string('category')->default('general')->change();
            $table->decimal('original_price', 10, 2)->nullable()->default(null)->change();
            
            // Only modify review_count if it exists
            if (Schema::hasColumn('tours', 'review_count')) {
                $table->integer('review_count')->default(0)->change();
            }
            
            $table->json('highlights')->default(json_encode([]))->change();
            $table->json('includes')->default(json_encode([]))->change();
            $table->json('itinerary')->default(json_encode([]))->change();
            $table->json('departure')->default(json_encode([]))->change();
            $table->integer('max_guests')->default(1)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            // Revert changes if needed
        });
    }
};
