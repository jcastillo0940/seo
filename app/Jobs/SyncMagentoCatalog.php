<?php

namespace App\Jobs;

use App\Models\Project;
use App\Services\MagentoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncMagentoCatalog implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Project $project,
    ) {
    }

    public function handle(MagentoService $magentoService): void
    {
        $pages = $magentoService->syncCatalog($this->project->fresh());

        foreach ($pages as $page) {
            $this->project->catalogPages()->updateOrCreate(
                [
                    'type' => $page['type'],
                    'external_id' => $page['external_id'],
                ],
                $page,
            );
        }

        $this->project->forceFill([
            'magento_last_synced_at' => now(),
        ])->save();
    }
}
