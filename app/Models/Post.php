<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image',
        'is_published',
        'reactions_count',
        'comments_count',
        'metadata'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'metadata' => 'array',
        'reactions_count' => 'integer',
        'comments_count' => 'integer'
    ];

    protected $with = ['user']; // Always load user relationship

    /**
     * Get the user that owns the post
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all reactions for the post
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(PostReaction::class);
    }

    /**
     * Get all comments for the post
     */
    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    /**
     * Get top-level comments (no parent)
     */
    public function topLevelComments(): HasMany
    {
        return $this->hasMany(PostComment::class)->whereNull('parent_id')->with('user', 'replies');
    }

    /**
     * Scope for published posts only
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for latest posts first
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get reaction counts by type
     */
    public function getReactionCountsByType()
    {
        return $this->reactions()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Check if user has reacted to this post
     */
    public function hasUserReacted($userId, $type = null)
    {
        $query = $this->reactions()->where('user_id', $userId);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->exists();
    }

    /**
     * Get user's reaction to this post
     */
    public function getUserReaction($userId)
    {
        return $this->reactions()->where('user_id', $userId)->first();
    }

    /**
     * Update reaction counts cache
     */
    public function updateReactionCount()
    {
        $this->update([
            'reactions_count' => $this->reactions()->count()
        ]);
    }

    /**
     * Update comments count cache
     */
    public function updateCommentsCount()
    {
        $this->update([
            'comments_count' => $this->comments()->count()
        ]);
    }

    /**
     * Get formatted created date
     */
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return '/storage/' . $this->image;
        }
        return null;
    }
}
