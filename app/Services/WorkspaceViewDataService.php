<?php

namespace App\Services;

use App\Models\Project;
use App\Models\SerpResult;
use App\Models\User;
use Illuminate\Support\Collection;
use Throwable;

class WorkspaceViewDataService
{
    public function forUser(User $user): array
    {
        $project = $user->projects()->latest()->with(['technicalAudits', 'competitors', 'trackedKeywords'])->first();
        $latestAudit = $project?->technicalAudits()->latest('audited_at')->first();
        $latestSerpSnapshot = $project?->serpSnapshots()->with(['trackedKeyword', 'results.competitor'])->latest('captured_at')->first();
        $latestCrawlRun = $project?->crawlRuns()->latest('finished_at')->first();
        $latestCrawlPages = $latestCrawlRun?->pages()->get() ?? collect();
        $latestCrawlIssues = collect($latestCrawlRun?->pages()->whereNotNull('issues')->get() ?? [])
            ->flatMap(fn ($page) => collect($page->issues ?? [])->map(fn ($issue) => [
                'url' => $page->url,
                'label' => $issue['label'] ?? 'Issue',
                'code' => $issue['code'] ?? 'issue',
                'severity' => $issue['severity'] ?? 'warn',
            ]))
            ->take(12)
            ->values();
        $topCatalogPages = $project?->catalogPages()->orderByDesc('product_count')->limit(6)->get() ?? collect();
        $topOrganicPages = $project?->analyticsPageMetrics()
            ->select('page_path', 'page_title')
            ->selectRaw('SUM(sessions) as sessions, SUM(conversions) as conversions')
            ->groupBy('page_path', 'page_title')
            ->orderByDesc('sessions')
            ->limit(6)
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
        $latestSnapshots = collect();
        $trackedKeywordsByIntent = collect();

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
                ->limit(12)
                ->get();

            $quickWins = $project->keywordMetrics()
                ->select('keyword')
                ->selectRaw('SUM(impressions) as impressions, SUM(clicks) as clicks, AVG(position) as avg_position')
                ->groupBy('keyword')
                ->havingRaw('AVG(position) BETWEEN 11 AND 20')
                ->orderByDesc('impressions')
                ->limit(10)
                ->get();

            $latestSnapshots = $project->serpSnapshots()
                ->with(['trackedKeyword', 'results'])
                ->latest('captured_at')
                ->limit(8)
                ->get();

            $trackedKeywordsByIntent = $project->trackedKeywords()
                ->selectRaw("COALESCE(search_intent, 'unassigned') as intent, COUNT(*) as total")
                ->groupBy('intent')
                ->orderByDesc('total')
                ->get();
        }

        try {
            $properties = app(GoogleConsoleService::class)->listProperties($user);
        } catch (Throwable $exception) {
            $propertyError = $exception->getMessage();
        }

        $competitors = $project
            ? $this->competitorsWithMetrics($project)
            : collect();
        $trackedKeywords = $project?->trackedKeywords()->orderBy('priority')->orderBy('keyword')->get() ?? collect();
        $projectDomain = $this->projectDomain($project);
        $issueBuckets = $this->issueBuckets($latestCrawlPages);
        $priorityCrawlPage = $this->priorityCrawlPage($latestCrawlPages);
        $connectionStatus = $this->connectionStatus($project, $latestCrawlRun);
        $serpOverview = $this->serpOverview($latestSnapshots);

        return [
            'user' => $user,
            'project' => $project,
            'projectDomain' => $projectDomain,
            'latestAudit' => $latestAudit,
            'properties' => $properties,
            'propertyError' => $propertyError,
            'topKeywords' => $topKeywords,
            'quickWins' => $quickWins,
            'competitors' => $competitors,
            'trackedKeywords' => $trackedKeywords,
            'latestSerpSnapshot' => $latestSerpSnapshot,
            'latestSnapshots' => $latestSnapshots,
            'latestCrawlRun' => $latestCrawlRun,
            'latestCrawlPages' => $latestCrawlPages,
            'latestCrawlIssues' => $latestCrawlIssues,
            'priorityCrawlPage' => $priorityCrawlPage,
            'issueBuckets' => $issueBuckets,
            'topCatalogPages' => $topCatalogPages,
            'topOrganicPages' => $topOrganicPages,
            'pageOpportunities' => collect($opportunities['page_opportunities']),
            'keywordOpportunities' => collect($opportunities['keyword_opportunities']),
            'competitorGaps' => collect($opportunities['competitor_gaps']),
            'trackedKeywordsByIntent' => $trackedKeywordsByIntent,
            'connectionStatus' => $connectionStatus,
            'serpOverview' => $serpOverview,
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
                'audit_score' => (int) ($latestAudit?->seo_score ?? 0),
                'crawl_issues' => (int) ($latestCrawlRun?->issue_count ?? 0),
            ],
            'crawlSeveritySummary' => $this->severitySummary($latestCrawlIssues),
        ];
    }

    protected function projectDomain(?Project $project): string
    {
        if (! $project) {
            return 'Sin proyecto';
        }

        $host = parse_url((string) $project->url, PHP_URL_HOST);

        return $host ?: $project->name;
    }

    protected function severitySummary(Collection $issues): array
    {
        return [
            'ok' => 0,
            'warn' => (int) $issues->where('severity', 'warn')->count(),
            'error' => (int) $issues->where('severity', 'error')->count(),
        ];
    }

    protected function issueBuckets(Collection $pages): Collection
    {
        return $pages
            ->flatMap(fn ($page) => collect($page->issues ?? [])->map(fn ($issue) => [
                'code' => $issue['code'] ?? 'issue',
                'label' => $issue['label'] ?? 'Issue',
                'severity' => $issue['severity'] ?? 'warn',
            ]))
            ->groupBy('code')
            ->map(function (Collection $group) {
                $first = $group->first();

                return [
                    'code' => $first['code'],
                    'label' => $first['label'],
                    'severity' => $first['severity'],
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();
    }

    protected function priorityCrawlPage(Collection $pages): ?array
    {
        $page = $pages
            ->sortByDesc(fn ($crawlPage) => count($crawlPage->issues ?? []))
            ->first();

        if (! $page) {
            return null;
        }

        return [
            'url' => $page->url,
            'path' => parse_url((string) $page->url, PHP_URL_PATH) ?: '/',
            'status_code' => $page->status_code,
            'title' => $page->title,
            'meta_description' => $page->meta_description,
            'h1' => $page->h1,
            'canonical_url' => $page->canonical_url,
            'robots_directives' => $page->robots_directives,
            'is_indexable' => (bool) $page->is_indexable,
            'is_in_sitemap' => (bool) $page->is_in_sitemap,
            'internal_links_count' => (int) $page->internal_links_count,
            'images_without_alt_count' => (int) $page->images_without_alt_count,
            'word_count' => (int) $page->word_count,
            'issues' => collect($page->issues ?? []),
        ];
    }

    protected function connectionStatus(?Project $project, $latestCrawlRun): array
    {
        return [
            'search_console' => [
                'status' => $project?->google_property_id ? 'connected' : 'missing',
                'label' => $project?->google_property_id ? 'conectado' : 'pendiente',
            ],
            'ga4' => [
                'status' => $project?->ga4_property_id ? 'connected' : 'missing',
                'label' => $project?->ga4_property_id ? 'conectado' : 'pendiente',
            ],
            'magento' => [
                'status' => $project?->magento_base_url ? 'connected' : 'missing',
                'label' => $project?->magento_base_url ? 'conectado' : 'pendiente',
            ],
            'crawler' => [
                'status' => $latestCrawlRun ? 'connected' : 'missing',
                'label' => $latestCrawlRun ? ($latestCrawlRun->status ?: 'activo') : 'pendiente',
            ],
        ];
    }

    protected function competitorsWithMetrics(Project $project): Collection
    {
        $competitors = $project->competitors()->orderBy('domain')->get();

        if ($competitors->isEmpty()) {
            return $competitors;
        }

        $metrics = SerpResult::query()
            ->join('serp_snapshots', 'serp_results.serp_snapshot_id', '=', 'serp_snapshots.id')
            ->where('serp_snapshots.project_id', $project->id)
            ->whereIn('serp_results.competitor_id', $competitors->pluck('id'))
            ->selectRaw('
                serp_results.competitor_id,
                COUNT(DISTINCT serp_snapshots.tracked_keyword_id) as keywords_count,
                ROUND(AVG(serp_results.position), 1) as avg_position,
                MIN(serp_results.position) as best_position
            ')
            ->groupBy('serp_results.competitor_id')
            ->get()
            ->keyBy('competitor_id');

        return $competitors->each(function ($competitor) use ($metrics) {
            $m = $metrics->get($competitor->id);
            $competitor->keywords_count = (int) ($m?->keywords_count ?? 0);
            $competitor->avg_position = $m ? (float) $m->avg_position : null;
            $competitor->best_position = $m ? (int) $m->best_position : null;
        });
    }

    protected function serpOverview(Collection $snapshots): array
    {
        $ownResults = $snapshots
            ->map(function ($snapshot) {
                $own = $snapshot->results->firstWhere('is_own_domain', true);

                return [
                    'keyword' => $snapshot->trackedKeyword?->keyword,
                    'captured_at' => $snapshot->captured_at,
                    'own_position' => $own?->position,
                ];
            })
            ->filter(fn ($row) => filled($row['keyword']));

        $top3 = $ownResults->filter(fn ($row) => $row['own_position'] && $row['own_position'] <= 3)->count();
        $top10 = $ownResults->filter(fn ($row) => $row['own_position'] && $row['own_position'] <= 10)->count();
        $outside = $ownResults->filter(fn ($row) => ! $row['own_position'] || $row['own_position'] > 10)->count();

        return [
            'top3' => $top3,
            'top10' => $top10,
            'outsideTop10' => $outside,
        ];
    }
}
