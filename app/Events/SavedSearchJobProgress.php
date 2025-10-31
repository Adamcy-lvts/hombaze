<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SavedSearchJobProgress
{
    use Dispatchable, SerializesModels;

    public int $userId;
    public int $searchId;
    public string $stage;
    public int $currentStep;
    public int $totalSteps;
    public string $message;
    public ?array $data;

    /**
     * Create a new event instance.
     */
    public function __construct(
        int $userId,
        int $searchId,
        string $stage,
        int $currentStep,
        int $totalSteps,
        string $message,
        ?array $data = null
    ) {
        $this->userId = $userId;
        $this->searchId = $searchId;
        $this->stage = $stage;
        $this->currentStep = $currentStep;
        $this->totalSteps = $totalSteps;
        $this->message = $message;
        $this->data = $data;
    }

}
