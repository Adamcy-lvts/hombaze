<?php

namespace App\Jobs;

use App\Models\Property;
use App\Models\PropertyView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdatePropertyViewCountJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $propertyId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $propertyId)
    {
        $this->propertyId = $propertyId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Calculate the current view count for this property
            $viewCount = PropertyView::where('property_id', $this->propertyId)->count();

            // Update the cached count in the properties table
            Property::where('id', $this->propertyId)
                   ->update(['view_count' => $viewCount]);

            Log::info("Updated view count for property {$this->propertyId}: {$viewCount} views");

        } catch (\Exception $e) {
            Log::error("Failed to update view count for property {$this->propertyId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }
}
