<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'type'
    ];

    // Reaction type constants
    const TYPE_LIKE = 'like';
    const TYPE_LOVE = 'love';
    const TYPE_ECO_LOVE = 'eco_love';
    const TYPE_RECYCLE = 'recycle';
    const TYPE_EARTH_DAY = 'earth_day';
    const TYPE_GREEN_ENERGY = 'green_energy';
    const TYPE_DISLIKE = 'dislike';

    // Available reaction types
    const AVAILABLE_TYPES = [
        self::TYPE_LIKE,
        self::TYPE_LOVE,
        self::TYPE_ECO_LOVE,
        self::TYPE_RECYCLE,
        self::TYPE_EARTH_DAY,
        self::TYPE_GREEN_ENERGY,
        self::TYPE_DISLIKE,
    ];

    /**
     * Get the user that owns the reaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post that the reaction belongs to
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get reaction icon and label
     */
    public function getReactionDetailsAttribute()
    {
        $details = [
            self::TYPE_LIKE => ['icon' => '👍', 'label' => 'Like', 'color' => 'text-blue-600'],
            self::TYPE_LOVE => ['icon' => '❤️', 'label' => 'Love', 'color' => 'text-red-600'],
            self::TYPE_ECO_LOVE => ['icon' => '🌱', 'label' => 'Eco Love', 'color' => 'text-green-600'],
            self::TYPE_RECYCLE => ['icon' => '♻️', 'label' => 'Recycle', 'color' => 'text-emerald-600'],
            self::TYPE_EARTH_DAY => ['icon' => '🌍', 'label' => 'Earth Day', 'color' => 'text-blue-500'],
            self::TYPE_GREEN_ENERGY => ['icon' => '⚡', 'label' => 'Green Energy', 'color' => 'text-yellow-500'],
            self::TYPE_DISLIKE => ['icon' => '👎', 'label' => 'Dislike', 'color' => 'text-gray-600'],
        ];

        return $details[$this->type] ?? ['icon' => '❓', 'label' => 'Unknown', 'color' => 'text-gray-400'];
    }

    /**
     * Get all available reaction types with details
     */
    public static function getAvailableReactionsWithDetails()
    {
        return [
            self::TYPE_LIKE => ['icon' => '👍', 'label' => 'Like', 'color' => 'text-blue-600'],
            self::TYPE_LOVE => ['icon' => '❤️', 'label' => 'Love', 'color' => 'text-red-600'],
            self::TYPE_ECO_LOVE => ['icon' => '🌱', 'label' => 'Eco Love', 'color' => 'text-green-600'],
            self::TYPE_RECYCLE => ['icon' => '♻️', 'label' => 'Recycle', 'color' => 'text-emerald-600'],
            self::TYPE_EARTH_DAY => ['icon' => '🌍', 'label' => 'Earth Day', 'color' => 'text-blue-500'],
            self::TYPE_GREEN_ENERGY => ['icon' => '⚡', 'label' => 'Green Energy', 'color' => 'text-yellow-500'],
            self::TYPE_DISLIKE => ['icon' => '👎', 'label' => 'Dislike', 'color' => 'text-gray-600'],
        ];
    }
}
