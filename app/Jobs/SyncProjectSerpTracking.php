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
        if (! config('seo.demo_mode') && (! in_array(config('services.serp.provider'), ['serpapi', 'google']) || blank(config('services.serp.api_key')))) {
            $this->delete();

            return;
        }

        $serpTrackingService->syncProject($this->project->fresh());
    }
}
