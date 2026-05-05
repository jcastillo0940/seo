<?php

namespace App\Jobs;

use App\Models\Project;
use App\Services\GoogleAnalyticsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class SyncGoogleAnalyticsPages implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Project $project,
    ) {
    }

    public function handle(GoogleAnalyticsService $googleAnalyticsService): void
    {
        $endDate = now()->toImmutable();
        $startDate = $endDate->subDays((int) config('seo.lookback_days', 30) - 1);

        $rows = $googleAnalyticsService->fetchOrganicLandingPages(
            $this->project->fresh('user'),
            Carbon::parse($startDate),
            Carbon::parse($endDate),
        );

        foreach ($rows as $row) {
            $this->project->analyticsPageMetrics()->updateOrCreate(
                [
                    'date' => $row['date'],
                    'page_path' => $row['page_path'],
                    'channel_group' => $row['channel_group'],
                ],
                [
                    'page_title' => $row['page_title'],
                    'sessions' => $row['sessions'],
                    'users' => $row['users'],
                    'conversions' => $row['conversions'],
                ]
            );
        }
    }
}
