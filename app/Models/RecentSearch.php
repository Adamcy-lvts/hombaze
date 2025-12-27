<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecentSearch extends Model
{
    protected $fillable = [
        'user_id',
        'term',
        'filters',
        'result_count',
    ];

    protected $casts = [
        'filters' => 'array',
        'result_count' => 'integer',
    ];

    /**
     * Get the user that made this search.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a search for a user.
     * Updates existing search or creates new one, pruning old searches.
     */
    public static function record(User $user, string $term, ?array $filters = null, int $resultCount = 0): self
    {
        $search = static::updateOrCreate(
            [
                'user_id' => $user->id,
                'term' => $term,
            ],
            [
                'filters' => $filters,
                'result_count' => $resultCount,
                'updated_at' => now(),
            ]
        );

        // Prune old searches - keep only last 10
        static::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->skip(10)
            ->take(100)
            ->delete();

        return $search;
    }

    /**
     * Get recent searches for a user.
     */
    public static function forUser(User $user, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Clear all searches for a user.
     */
    public static function clearForUser(User $user): int
    {
        return static::where('user_id', $user->id)->delete();
    }
}
