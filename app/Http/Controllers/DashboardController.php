<?php

namespace App\Http\Controllers;

use App\Jobs\RunPageSpeedAudit;
use App\Jobs\RunSeoCrawl;
use App\Jobs\SyncGoogleAnalyticsPages;
use App\Jobs\SyncProjectKeywordMetrics;
use App\Jobs\SyncProjectSerpTracking;
use App\Services\WorkspaceViewDataService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected WorkspaceViewDataService $workspaceViewDataService
    ) {
    }

    public function __invoke(): View
    {
        return view('dashboard', $this->workspaceViewDataService->forUser(request()->user()));
    }

    public function sync(): RedirectResponse
    {
        $project = request()->user()->projects()->latest()->firstOrFail();

        SyncProjectKeywordMetrics::dispatch($project);

        if (config('seo.demo_mode') || filled($project->ga4_property_id)) {
            SyncGoogleAnalyticsPages::dispatch($project);
        }

        if ($project->trackedKeywords()->exists() || config('seo.demo_mode')) {
            SyncProjectSerpTracking::dispatch($project);
        }

        return back()->with('status', 'Sincronizacion Google en cola. Se preparan Search Console, GA4 y tracking SERP cuando aplique.');
    }

    public function audit(): RedirectResponse
    {
        $project = request()->user()->projects()->latest()->firstOrFail();

        RunPageSpeedAudit::dispatch($project);

        return back()->with('status', 'Auditoria tecnica en cola. Ejecuta `php artisan queue:work` para procesarla.');
    }

    public function crawl(): RedirectResponse
    {
        $project = request()->user()->projects()->latest()->firstOrFail();

        RunSeoCrawl::dispatch($project);

        return back()->with('status', 'Crawler SEO en cola. Ejecuta `php artisan queue:work` para procesarlo.');
    }
}
