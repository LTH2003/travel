<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Drop the columns
            $table->dropColumn([
                'bed_type',
                'area',
                'bathroom',
                'image',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Restore the columns if rollback
            $table->string('bed_type')->nullable()->after('beds');
            $table->integer('area')->nullable()->after('bed_type');
            $table->string('bathroom')->nullable()->after('area');
            $table->longText('image')->nullable()->after('bathroom');
        });
    }
};
