<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add tour_manager and hotel_manager roles to users who have 'user' role
        // This migration is just a marker - roles are managed through validation
        // Possible roles: user, admin, tour_manager, hotel_manager
    }

    public function down(): void
    {
        // Roles are validated in backend, no database changes needed
    }
};
