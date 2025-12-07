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
        // Check if columns exist before adding
        if (!Schema::hasColumn('contacts', 'response')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->longText('response')->nullable()->after('message');
                $table->unsignedBigInteger('responded_by')->nullable()->after('response');
                $table->timestamp('responded_at')->nullable()->after('responded_by');
                
                // Add soft delete if not exists
                if (!Schema::hasColumn('contacts', 'deleted_at')) {
                    $table->softDeletes()->after('responded_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Drop columns in reverse order to avoid foreign key issues
            if (Schema::hasColumn('contacts', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
            if (Schema::hasColumn('contacts', 'responded_at')) {
                $table->dropColumn('responded_at');
            }
            if (Schema::hasColumn('contacts', 'responded_by')) {
                $table->dropColumn('responded_by');
            }
            if (Schema::hasColumn('contacts', 'response')) {
                $table->dropColumn('response');
            }
        });
    }
};
