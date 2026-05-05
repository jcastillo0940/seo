<?php

namespace App\Jobs;

use App\Models\Project;
use App\Services\SeoCrawlerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RunSeoCrawl implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Project $project,
    ) {
    }

    public function handle(SeoCrawlerService $seoCrawlerService): void
    {
        $crawlRun = $this->project->crawlRuns()->create([
            'source' => 'catalog',
            'status' => 'running',
            'started_at' => now(),
        ]);

        $result = $seoCrawlerService->crawl($this->project->fresh());

        foreach ($result['pages'] as $page) {
            $crawlRun->pages()->create([
                ...$page,
                'project_id' => $this->project->id,
            ]);
        }

        $crawlRun->update([
            'status' => $result['status'],
            'pages_crawled' => $result['pages_crawled'],
            'issue_count' => $result['issue_count'],
            'summary' => $result['summary'],
            'finished_at' => now(),
        ]);
    }
}
