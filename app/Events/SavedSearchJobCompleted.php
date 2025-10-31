<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SavedSearchJobCompleted
{
    use Dispatchable, SerializesModels;

    public int $userId;
    public int $searchId;
    public bool $success;
    public int $matchCount;
    public string $message;

    /**
     * Create a new event instance.
     */
    public function __construct(int $userId, int $searchId, bool $success, int $matchCount, string $message)
    {
        $this->userId = $userId;
        $this->searchId = $searchId;
        $this->success = $success;
        $this->matchCount = $matchCount;
        $this->message = $message;
    }

}
