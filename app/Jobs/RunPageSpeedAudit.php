<?php

namespace App\Jobs;

use App\Models\Project;
use App\Services\PageSpeedService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RunPageSpeedAudit implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Project $project,
    ) {
    }

    public function handle(PageSpeedService $pageSpeedService): void
    {
        $audit = $pageSpeedService->run($this->project);

        $this->project->technicalAudits()->create([
            'performance_score' => $audit['performance_score'],
            'seo_score' => $audit['seo_score'],
            'json_raw_data' => $audit['raw_data'],
            'audited_at' => now(),
        ]);
    }
}
