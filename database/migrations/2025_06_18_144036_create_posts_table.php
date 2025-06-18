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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('image')->nullable(); // Path to uploaded image
            $table->boolean('is_published')->default(true);
            $table->integer('reactions_count')->default(0); // Cache for performance
            $table->integer('comments_count')->default(0); // Cache for performance
            $table->json('metadata')->nullable(); // For future extensibility
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'created_at']);
            $table->index(['is_published', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
