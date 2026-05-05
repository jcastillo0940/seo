<?php

namespace App\Jobs;

use App\Models\Project;
use App\Services\SerpTrackingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncProjectSerpTracking implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Project $project,
    ) {
    }

    public function handle(SerpTrackingService $serpTrackingService): void
    {
        $serpTrackingService->syncProject($this->project->fresh());
    }
}
