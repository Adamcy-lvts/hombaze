<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class PropertyInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'interaction_type',
        'interaction_score',
        'metadata',
        'source',
        'session_id',
        'interaction_date',
    ];

    protected $casts = [
        'metadata' => 'array',
        'interaction_score' => 'decimal:2',
        'interaction_date' => 'datetime',
    ];

    // Interaction scoring weights
    public const INTERACTION_SCORES = [
        'view' => 3.0,
        'inquiry' => 8.0,
        'viewing_scheduled' => 7.0,
        'viewing_completed' => 10.0,
        'viewing_cancelled' => 2.0,
        'contact_agent' => 6.0,
        'save_property' => 5.0,
        'share_property' => 4.0,
        'report_property' => 1.0,
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('interaction_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('interaction_date', '>=', Carbon::now()->subDays($days));
    }

    public function scopeHighEngagement($query, $minScore = 7.0)
    {
        return $query->where('interaction_score', '>=', $minScore);
    }

    // Helper methods
    public static function trackInteraction(
        int $userId,
        int $propertyId,
        string $interactionType,
        array $metadata = [],
        ?string $source = null,
        ?string $sessionId = null
    ): self {
        $score = self::INTERACTION_SCORES[$interactionType] ?? 1.0;

        // Apply time decay for repeated interactions
        $existingInteraction = self::where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->where('interaction_type', $interactionType)
            ->orderBy('interaction_date', 'desc')
            ->first();

        if ($existingInteraction) {
            $daysSinceLastInteraction = $existingInteraction->interaction_date->diffInDays(now());
            if ($daysSinceLastInteraction < 1) {
                $score *= 0.5; // Reduce score for repeated same-day interactions
            }
        }

        return self::create([
            'user_id' => $userId,
            'property_id' => $propertyId,
            'interaction_type' => $interactionType,
            'interaction_score' => $score,
            'metadata' => $metadata,
            'source' => $source,
            'session_id' => $sessionId,
            'interaction_date' => now(),
        ]);
    }

    public function getTimeDecayedScore(int $decayDays = 7): float
    {
        $daysOld = $this->interaction_date->diffInDays(now());
        $decayRate = 0.2; // 20% decay per week
        $weeksOld = $daysOld / $decayDays;

        return $this->interaction_score * pow(1 - $decayRate, $weeksOld);
    }

    public static function getUserEngagementScore(int $userId, int $days = 30): float
    {
        return self::forUser($userId)
            ->recent($days)
            ->get()
            ->sum(function ($interaction) {
                return $interaction->getTimeDecayedScore();
            });
    }

    public static function getPropertyPopularityScore(int $propertyId, int $days = 30): float
    {
        return self::forProperty($propertyId)
            ->recent($days)
            ->get()
            ->sum(function ($interaction) {
                return $interaction->getTimeDecayedScore();
            });
    }

    public static function getUserPropertyAffinity(int $userId, int $propertyId): float
    {
        return self::forUser($userId)
            ->forProperty($propertyId)
            ->get()
            ->sum(function ($interaction) {
                return $interaction->getTimeDecayedScore();
            });
    }
}
