<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add username field (unique)
            $table->string('username', 50)->unique()->after('name');

            // Add role field with enum
            $table->enum('role', ['user', 'moderator', 'admin'])->default('user')->after('email_verified_at');

            // Add status field
            $table->enum('status', ['active', 'banned', 'suspended'])->default('active')->after('role');

            // Add profile fields
            $table->string('profile_image')->nullable()->after('status');
            $table->text('bio')->nullable()->after('profile_image');
            $table->string('phone', 20)->nullable()->after('bio');

            // Add eco score
            $table->integer('eco_score')->default(0)->after('phone');

            // Add language preference
            $table->enum('language', ['en', 'de'])->default('en')->after('eco_score');

            // Add indexes for performance
            $table->index(['role']);
            $table->index(['status']);
            $table->index(['eco_score']);
            $table->index(['username']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['status']);
            $table->dropIndex(['eco_score']);
            $table->dropIndex(['username']);

            $table->dropColumn([
                'username',
                'role',
                'status',
                'profile_image',
                'bio',
                'phone',
                'eco_score',
                'language'
            ]);
        });
    }
};