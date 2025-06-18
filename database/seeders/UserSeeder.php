<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing users (optional - be careful in production!)
        // User::truncate();

        // 1. Create 1 Admin with real email
        User::factory()->admin()->create();

        // 2. Create 4 Moderators (3 fake emails + 1 real email)
        User::factory()->testUser('moderator', 'mod1')->create();
        User::factory()->testUser('moderator', 'mod2')->create();
        User::factory()->testUser('moderator', 'mod3')->create();
        User::factory()->realModeratorUser()->create(); // Real email: sanadhakouz@ymail.com

        // 3. Create 45 Regular Users (44 fake emails + 1 real email)
        User::factory(44)->create(); // 44 fake users
        User::factory()->realRegularUser()->create(); // Real email: jzokah@yahoo.com

        // Output summary
        $this->command->info('Created users with email distribution:');
        $this->command->info('');
        $this->command->info('👤 ADMIN (1 user with real email):');
        $this->command->info('   - sanadhakooz@ymail.com / adiga123');
        $this->command->info('');
        $this->command->info('🛡️  MODERATORS (4 users - 3 fake + 1 real):');
        $this->command->info('   - mod1@test.com / adiga123 (fake)');
        $this->command->info('   - mod2@test.com / adiga123 (fake)');
        $this->command->info('   - mod3@test.com / adiga123 (fake)');
        $this->command->info('   - sanadhakouz@ymail.com / adiga123 (REAL)');
        $this->command->info('');
        $this->command->info('👥 REGULAR USERS (45 users - 44 fake + 1 real):');
        $this->command->info('   - 44 users with fake emails / adiga123');
        $this->command->info('   - jzokah@yahoo.com / adiga123 (REAL)');
        $this->command->info('');
        $this->command->info('📊 TOTAL: 50 users created successfully!');
        $this->command->info('📧 Real emails: 3 (perfect for email testing!)');
    }
}