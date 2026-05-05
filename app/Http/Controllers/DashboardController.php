<?php

namespace App\Http\Controllers;

use App\Jobs\RunPageSpeedAudit;
use App\Jobs\RunSeoCrawl;
use App\Jobs\SyncGoogleAnalyticsPages;
use App\Jobs\SyncProjectKeywordMetrics;
use App\Jobs\SyncProjectSerpTracking;
use App\Services\SeoOpportunityService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Throwable;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user();
        $project = $user->projects()->latest()->with(['technicalAudits', 'competitors', 'trackedKeywords'])->first();
        $latestAudit = $project?->technicalAudits()->latest('audited_at')->first();
        $latestSerpSnapshot = $project?->serpSnapshots()->with(['trackedKeyword', 'results.competitor'])->latest('captured_at')->first();
        $latestCrawlRun = $project?->crawlRuns()->latest('finished_at')->first();
        $latestCrawlIssues = collect($latestCrawlRun?->pages()->whereNotNull('issues')->get() ?? [])
            ->flatMap(fn ($page) => collect($page->issues ?? [])->map(fn ($issue) => [
                'url' => $page->url,
                'label' => $issue['label'] ?? 'Issue',
                'code' => $issue['code'] ?? 'issue',
            ]))
            ->take(8)
            ->values();
        $topCatalogPages = $project?->catalogPages()->orderByDesc('product_count')->limit(5)->get() ?? collect();
        $topOrganicPages = $project?->analyticsPageMetrics()
            ->select('page_path', 'page_title')
            ->selectRaw('SUM(sessions) as sessions, SUM(conversions) as conversions')
            ->groupBy('page_path', 'page_title')
            ->orderByDesc('sessions')
            ->limit(5)
            ->get() ?? collect();
        $opportunities = $project ? app(SeoOpportunityService::class)->forProject($project) : [
            'page_opportunities' => collect(),
            'keyword_opportunities' => collect(),
            'competitor_gaps' => collect(),
        ];
        $propertyError = null;

        $chartRows = collect();
        $topKeywords = collect();
        $quickWins = collect();
        $properties = [];

        if ($project) {
            $chartRows = $project->keywordMetrics()
                ->selectRaw('date, SUM(clicks) as clicks, SUM(impressions) as impressions')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $topKeywords = $project->keywordMetrics()
                ->select('keyword')
                ->selectRaw('SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(position) as avg_position')
                ->groupBy('keyword')
                ->orderByDesc('clicks')
                ->limit(10)
                ->get();

            $quickWins = $project->keywordMetrics()
                ->select('keyword')
                ->selectRaw('SUM(impressions) as impressions, SUM(clicks) as clicks, AVG(position) as avg_position')
                ->groupBy('keyword')
                ->havingRaw('AVG(position) BETWEEN 11 AND 20')
                ->orderByDesc('impressions')
                ->limit(10)
                ->get();
        }

        try {
            $properties = app(\App\Services\GoogleConsoleService::class)->listProperties($user);
        } catch (Throwable $exception) {
            $propertyError = $exception->getMessage();
        }

        return view('dashboard', [
            'user' => $user,
            'project' => $project,
            'latestAudit' => $latestAudit,
            'properties' => $properties,
            'propertyError' => $propertyError,
            'topKeywords' => $topKeywords,
            'quickWins' => $quickWins,
            'competitors' => $project?->competitors()->orderBy('domain')->get() ?? collect(),
            'trackedKeywords' => $project?->trackedKeywords()->orderBy('priority')->orderBy('keyword')->get() ?? collect(),
            'latestSerpSnapshot' => $latestSerpSnapshot,
            'latestCrawlRun' => $latestCrawlRun,
            'latestCrawlIssues' => $latestCrawlIssues,
            'topCatalogPages' => $topCatalogPages,
            'topOrganicPages' => $topOrganicPages,
            'pageOpportunities' => collect($opportunities['page_opportunities']),
            'keywordOpportunities' => collect($opportunities['keyword_opportunities']),
            'competitorGaps' => collect($opportunities['competitor_gaps']),
            'chartLabels' => $chartRows->pluck('date')->map(fn ($date) => (string) $date)->all(),
            'chartClicks' => $chartRows->pluck('clicks')->map(fn ($value) => (int) $value)->all(),
            'chartImpressions' => $chartRows->pluck('impressions')->map(fn ($value) => (int) $value)->all(),
            'summary' => [
                'keywords' => (int) ($project?->keywordMetrics()->distinct()->count('keyword') ?? 0),
                'clicks' => (int) ($project?->keywordMetrics()->sum('clicks') ?? 0),
                'impressions' => (int) ($project?->keywordMetrics()->sum('impressions') ?? 0),
                'tracked_keywords' => (int) ($project?->trackedKeywords()->count() ?? 0),
                'competitors' => (int) ($project?->competitors()->count() ?? 0),
                'catalog_pages' => (int) ($project?->catalogPages()->count() ?? 0),
                'organic_pages' => (int) ($project?->analyticsPageMetrics()->distinct()->count('page_path') ?? 0),
                'serp_snapshots' => (int) ($project?->serpSnapshots()->count() ?? 0),
            ],
        ]);
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
