<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('adiga123'),
            'role' => 'user',
            'status' => 'active',
            'profile_image' => 'profile-pictures/' . $this->faker->randomElement(['pp1.JPG', 'pp2.JPG', 'pp3.JPG', 'pp4.JPG', 'pp5.JPG']),
            'bio' => $this->faker->optional(0.7)->sentence(10),
            'phone' => $this->faker->optional(0.5)->phoneNumber(),
            'eco_score' => $this->faker->numberBetween(0, 100),
            'language' => $this->faker->randomElement(['en', 'de']),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a moderator user.
     */
    public function moderator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'moderator',
            'profile_image' => 'profile-pictures/' . $this->faker->randomElement(['pp1.JPG', 'pp2.JPG', 'pp3.JPG', 'pp4.JPG', 'pp5.JPG']),
            'eco_score' => $this->faker->numberBetween(70, 100),
            'bio' => 'Community moderator helping to keep EcoTrack a positive space.',
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'profile_image' => 'profile-pictures/pp1.JPG',
            'eco_score' => 100,
            'bio' => 'EcoTrack administrator managing the platform.',
            'name' => 'Sanad Hakooz',
            'username' => 'sanadhakooz',
            'email' => 'sanadhakooz@ymail.com',
        ]);
    }

    /**
     * Create specific test users with known credentials.
     */
    public function testUser(string $role = 'user', ?string $username = null): static
    {
        $username = $username ?: $this->faker->userName();

        return $this->state(fn (array $attributes) => [
            'role' => $role,
            'username' => $username,
            'email' => $username . '@test.com',
            'name' => ucfirst($username) . ' Test',
            'profile_image' => 'profile-pictures/' . $this->faker->randomElement(['pp1.JPG', 'pp2.JPG', 'pp3.JPG', 'pp4.JPG', 'pp5.JPG']),
            'bio' => "Test {$role} account for EcoTrack development.",
        ]);
    }

    /**
     * Create the special moderator with real email.
     */
    public function realModeratorUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'moderator',
            'username' => 'sanadhakouz',
            'email' => 'sanadhakouz@ymail.com',
            'name' => 'Sanad Hakouz',
            'profile_image' => 'profile-pictures/pp2.JPG',
            'eco_score' => $this->faker->numberBetween(80, 95),
            'bio' => 'Real moderator account for testing email features.',
        ]);
    }

    /**
     * Create the special regular user with real email.
     */
    public function realRegularUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'user',
            'username' => 'jzokah',
            'email' => 'jzokah@yahoo.com',
            'name' => 'J Zokah',
            'profile_image' => 'profile-pictures/pp3.JPG',
            'eco_score' => $this->faker->numberBetween(40, 75),
            'bio' => 'Real user account for testing email features.',
        ]);
    }
}