<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'parent_id',
        'content',
        'is_edited',
        'edited_at'
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'edited_at' => 'datetime'
    ];

    protected $with = ['user']; // Always load user relationship

    /**
     * Get the user that owns the comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post that the comment belongs to
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the parent comment (for nested replies)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(PostComment::class, 'parent_id');
    }

    /**
     * Get the replies to this comment
     */
    public function replies(): HasMany
    {
        return $this->hasMany(PostComment::class, 'parent_id')->with('user');
    }

    /**
     * Scope for top-level comments only
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for latest comments first
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for oldest comments first
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Check if comment is a reply
     */
    public function isReply()
    {
        return !is_null($this->parent_id);
    }

    /**
     * Get formatted created date
     */
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Mark comment as edited
     */
    public function markAsEdited()
    {
        $this->update([
            'is_edited' => true,
            'edited_at' => now()
        ]);
    }

    /**
     * Update the post's comment count when comment is created/deleted
     */
    protected static function booted()
    {
        static::created(function ($comment) {
            $comment->post->updateCommentsCount();
        });

        static::deleted(function ($comment) {
            $comment->post->updateCommentsCount();
        });
    }
}
