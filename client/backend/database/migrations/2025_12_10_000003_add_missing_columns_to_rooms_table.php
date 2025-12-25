<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('rooms', 'bed_type')) {
                $table->string('bed_type')->nullable()->after('beds');
            }
            if (!Schema::hasColumn('rooms', 'area')) {
                $table->integer('area')->nullable()->after('bed_type');
            }
            if (!Schema::hasColumn('rooms', 'bathroom')) {
                $table->string('bathroom')->nullable()->after('area');
            }
            if (!Schema::hasColumn('rooms', 'image')) {
                $table->string('image')->nullable()->after('bathroom');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (Schema::hasColumn('rooms', 'bed_type')) {
                $table->dropColumn('bed_type');
            }
            if (Schema::hasColumn('rooms', 'area')) {
                $table->dropColumn('area');
            }
            if (Schema::hasColumn('rooms', 'bathroom')) {
                $table->dropColumn('bathroom');
            }
            if (Schema::hasColumn('rooms', 'image')) {
                $table->dropColumn('image');
            }
        });
    }
};
