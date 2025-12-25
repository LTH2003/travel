<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->longText('content');
            $table->unsignedBigInteger('parent_id')->nullable(); // For nested comments/replies
            $table->boolean('is_approved')->default(true); // Admin can approve comments
            $table->timestamps();

            // Foreign keys
            $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('blog_comments')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_comments');
    }
};
