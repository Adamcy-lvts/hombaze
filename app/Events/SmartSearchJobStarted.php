<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SmartSearchJobStarted
{
    use Dispatchable, SerializesModels;

    public int $userId;
    public int $searchId;
    public string $jobType;
    public string $message;

    /**
     * Create a new event instance.
     */
    public function __construct(int $userId, int $searchId, string $jobType, string $message = 'Search started...')
    {
        $this->userId = $userId;
        $this->searchId = $searchId;
        $this->jobType = $jobType;
        $this->message = $message;
    }

}
