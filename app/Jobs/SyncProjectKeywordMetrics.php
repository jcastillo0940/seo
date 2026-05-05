<?php

namespace App\Jobs;

use App\Models\Project;
use App\Services\GoogleConsoleService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class SyncProjectKeywordMetrics implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Project $project,
    ) {
    }

    public function handle(GoogleConsoleService $googleConsoleService): void
    {
        $endDate = now()->toImmutable();
        $startDate = $endDate->subDays((int) config('seo.lookback_days', 30) - 1);

        $rows = $googleConsoleService->fetchKeywordMetrics(
            $this->project->fresh('user'),
            Carbon::parse($startDate),
            Carbon::parse($endDate),
        );

        foreach ($rows as $row) {
            $this->project->keywordMetrics()->updateOrCreate(
                [
                    'keyword' => $row['keyword'],
                    'date' => $row['date'],
                ],
                [
                    'position' => $row['position'],
                    'clicks' => $row['clicks'],
                    'impressions' => $row['impressions'],
                ]
            );

            $this->project->trackedKeywords()->updateOrCreate(
                [
                    'keyword' => $row['keyword'],
                    'country_code' => 'US',
                    'language_code' => 'es',
                    'device' => 'mobile',
                ],
                [
                    'priority' => $row['position'] <= 10 ? 2 : 3,
                    'search_intent' => null,
                    'source' => 'search_console',
                ]
            );
        }

        $this->project->forceFill([
            'last_synced_at' => now(),
        ])->save();
    }
}
