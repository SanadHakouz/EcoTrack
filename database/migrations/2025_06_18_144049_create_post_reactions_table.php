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
        Schema::create('post_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'like',           // 👍 Standard like
                'love',           // ❤️ Love reaction
                'eco_love',       // 🌱 Eco love (plant/leaf icon)
                'recycle',        // ♻️ Recycle reaction
                'earth_day',      // 🌍 Earth day reaction
                'green_energy',   // ⚡ Green energy reaction
                'dislike'         // 👎 Dislike
            ]);
            $table->timestamps();

            // Ensure one reaction per user per post
            $table->unique(['user_id', 'post_id']);

            // Indexes for performance
            $table->index(['post_id', 'type']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_reactions');
    }
};
