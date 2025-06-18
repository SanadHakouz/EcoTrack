<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\PostComment;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->count() === 0) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        // Sample posts with eco-themed content
        $samplePosts = [
            [
                'title' => 'My Journey to Zero Waste',
                'content' => 'After 6 months of trying to reduce my waste, I\'ve finally achieved my goal of filling only one small jar with trash! 🌱 It wasn\'t easy, but small changes like using reusable bags, composting, and buying in bulk made all the difference. What zero waste tips do you have?',
            ],
            [
                'title' => 'Solar Panel Installation Complete!',
                'content' => 'Just installed solar panels on my roof and I\'m so excited! ☀️ The process took 3 days but it\'s going to reduce our carbon footprint significantly. Already seeing a reduction in our electricity bill. Who else has made the switch to renewable energy?',
            ],
            [
                'title' => 'Community Garden Success',
                'content' => 'Our neighborhood community garden is thriving! 🥕🌽 We\'ve grown over 200 pounds of organic vegetables this season and donated 50% to local food banks. Gardening together has brought our community closer while helping the environment.',
            ],
            [
                'title' => 'Plastic-Free July Challenge',
                'content' => 'Day 15 of Plastic-Free July and I\'m learning so much! 🚫🥤 The biggest challenge has been finding plastic-free packaging for groceries. I\'ve discovered some amazing local shops that offer bulk goods. Anyone else participating this year?',
            ],
            [
                'title' => 'Electric Vehicle Experience',
                'content' => 'Six months with my electric car and I\'m never going back! ⚡🚗 The savings on fuel costs are incredible, and knowing I\'m not contributing to air pollution feels amazing. The charging infrastructure has improved so much in our area.',
            ],
            [
                'title' => 'Homemade Cleaning Products',
                'content' => 'Made the switch to homemade cleaning products using simple ingredients like vinegar, baking soda, and essential oils. 🧽✨ Not only are they eco-friendly, but they\'re also much cheaper! My house smells like lavender instead of chemicals.',
            ],
            [
                'title' => 'Beach Cleanup Success',
                'content' => 'Today our group collected 847 pieces of trash from the local beach! 🏖️ Found everything from bottle caps to fishing nets. It\'s heartbreaking to see this pollution, but so rewarding to make a difference. Next cleanup is scheduled for next month!',
            ],
            [
                'title' => 'Composting Tips for Beginners',
                'content' => 'Starting a compost bin was intimidating at first, but it\'s actually quite simple! 🗂️ Key tips: maintain proper brown/green ratio, turn regularly, and keep it moist but not soggy. My plants have never been happier with this nutrient-rich soil!',
            ],
            [
                'title' => 'Sustainable Fashion Swap',
                'content' => 'Organized a clothing swap in my neighborhood and it was amazing! 👗♻️ 30 families participated and we diverted tons of clothes from landfills while everyone got "new" items. Planning to make this a quarterly event.',
            ],
            [
                'title' => 'Rain Water Harvesting Setup',
                'content' => 'Finally set up a rainwater harvesting system! 💧 During the last storm, I collected 150 gallons of water for my garden. It\'s incredible how much water we can save and reuse. The plants seem to love rainwater more than tap water too!',
            ],
        ];

        $ecoTitles = [
            'Reducing Carbon Footprint at Home',
            'Bike to Work Challenge Week 3',
            'DIY Natural Skincare Recipe',
            'Local Farmers Market Haul',
            'Energy Efficient Home Upgrades',
            'Upcycling Old Furniture Project',
            'Plant-Based Meal Prep Sunday',
            'Pollinator Garden Progress',
            'Reusable Water Bottle Collection',
            'Green Roof Installation Journey'
        ];

        // Create posts
        $createdPosts = [];

        foreach ($samplePosts as $index => $postData) {
            $user = $users->random();
            $post = Post::create([
                'user_id' => $user->id,
                'title' => $postData['title'],
                'content' => $postData['content'],
                'is_published' => true,
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
            ]);
            $createdPosts[] = $post;
        }

        // Add more posts with random titles
        for ($i = 0; $i < 15; $i++) {
            $user = $users->random();
            $title = $ecoTitles[array_rand($ecoTitles)];
            $contents = [
                "Just discovered this amazing eco-friendly tip and had to share! 🌱",
                "Making small changes every day to live more sustainably. Progress over perfection! 💚",
                "Who else is passionate about protecting our planet? Let's connect! 🌍",
                "Sharing my latest sustainable living experiment. Results are promising! ♻️",
                "Found this incredible local business that aligns with my environmental values! 🌿",
                "Another step closer to my zero-waste goals. Every little bit counts! 🗂️",
                "Learning so much from this amazing community. Thank you for the inspiration! 🙏",
                "Weekend project complete! Can't wait to see the environmental impact. ⚡",
            ];

            $post = Post::create([
                'user_id' => $user->id,
                'title' => $title,
                'content' => $contents[array_rand($contents)],
                'is_published' => true,
                'created_at' => now()->subDays(rand(0, 60))->subHours(rand(0, 23)),
            ]);
            $createdPosts[] = $post;
        }

        // Add reactions to posts
        $reactionTypes = PostReaction::AVAILABLE_TYPES;
        foreach ($createdPosts as $post) {
            // Random number of reactions per post (0-15)
            $reactionCount = rand(0, 15);
            $usersWhoReacted = $users->random(min($reactionCount, $users->count()));

            foreach ($usersWhoReacted as $user) {
                // Eco-themed reactions are more likely
                $weights = [
                    'like' => 25,
                    'love' => 15,
                    'eco_love' => 30,
                    'recycle' => 20,
                    'earth_day' => 15,
                    'green_energy' => 10,
                    'dislike' => 5,
                ];

                $reactionType = $this->weightedRandom($weights);

                PostReaction::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'type' => $reactionType,
                    'created_at' => $post->created_at->addMinutes(rand(1, 1440)), // Within 24 hours of post
                ]);
            }

            // Update reaction count
            $post->updateReactionCount();
        }

        // Add comments to posts
        $commentTexts = [
            "This is so inspiring! Thanks for sharing! 🌟",
            "I've been thinking about trying this myself. How did you get started?",
            "Amazing work! Keep it up! 💚",
            "This gives me hope for our planet's future! 🌍",
            "Such a great idea! I'm definitely going to try this.",
            "Love seeing people make a difference! ♻️",
            "This is exactly what we need more of. Thank you!",
            "You're an inspiration to our community! 🌱",
            "Fantastic results! Can you share more details?",
            "This made my day! So proud of our eco-warriors! ⚡",
            "I had no idea this was possible. Mind blown! 🤯",
            "Your dedication to sustainability is admirable! 👏",
            "This could work great in our neighborhood too!",
            "Thanks for the detailed explanation! Very helpful! 📚",
            "Your progress is motivating me to do better! 💪",
        ];

        foreach ($createdPosts as $post) {
            // Random number of comments per post (0-8)
            $commentCount = rand(0, 8);

            for ($i = 0; $i < $commentCount; $i++) {
                $commenter = $users->random();

                $comment = PostComment::create([
                    'user_id' => $commenter->id,
                    'post_id' => $post->id,
                    'content' => $commentTexts[array_rand($commentTexts)],
                    'created_at' => $post->created_at->addMinutes(rand(30, 2880)), // 30 minutes to 2 days after post
                ]);

                // Sometimes add replies to comments
                if (rand(1, 100) <= 30) { // 30% chance of reply
                    $replier = $users->random();
                    if ($replier->id !== $commenter->id) {
                        PostComment::create([
                            'user_id' => $replier->id,
                            'post_id' => $post->id,
                            'parent_id' => $comment->id,
                            'content' => [
                                "Thanks for the encouragement! 😊",
                                "Absolutely! Happy to help spread awareness! 🌱",
                                "Let me know if you have any questions!",
                                "Together we can make a bigger impact! 💚",
                                "I appreciate your support! 🙏",
                            ][array_rand([
                                "Thanks for the encouragement! 😊",
                                "Absolutely! Happy to help spread awareness! 🌱",
                                "Let me know if you have any questions!",
                                "Together we can make a bigger impact! 💚",
                                "I appreciate your support! 🙏",
                            ])],
                            'created_at' => $comment->created_at->addMinutes(rand(10, 480)), // 10 minutes to 8 hours after comment
                        ]);
                    }
                }
            }

            // Update comments count
            $post->updateCommentsCount();
        }

        $this->command->info('Created ' . count($createdPosts) . ' posts with reactions and comments!');
    }

    /**
     * Select a random item based on weights
     */
    private function weightedRandom($weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        foreach ($weights as $item => $weight) {
            $random -= $weight;
            if ($random <= 0) {
                return $item;
            }
        }

        return array_key_first($weights); // fallback
    }
}
