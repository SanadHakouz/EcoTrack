<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
        'profile_image',
        'bio',
        'phone',
        'eco_score',
        'language',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'eco_score' => 'integer',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeWithEcoScore($query, $minScore = 0)
    {
        return $query->where('eco_score', '>=', $minScore);
    }

    // Methods
    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    public function canModerate(): bool
    {
        return in_array($this->role, ['admin', 'moderator']);
    }

    public function isBanned(): bool
    {
        return $this->status === 'banned';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Relationships
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function postReactions()
    {
        return $this->hasMany(PostReaction::class);
    }

    public function postComments()
    {
        return $this->hasMany(PostComment::class);
    }

    // Future relationships (we'll add these later)
    // public function emissions() { return $this->hasMany(Emission::class); }
    // public function followers() { return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id'); }
}