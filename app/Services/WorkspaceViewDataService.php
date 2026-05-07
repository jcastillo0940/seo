<?php

namespace App\Services;

use App\Models\Project;
use App\Models\SerpResult;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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
                'severity' => $issue['severity'] ?? $this->severityForIssueCode($issue['code'] ?? 'issue'),
            ]))
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
        $properties = [];
        $latestSnapshots = collect();
        $trackedKeywordsByIntent = collect();
        $trackedKeywords = $project?->trackedKeywords()->orderBy('priority')->orderBy('keyword')->get() ?? collect();
        $locale = $this->resolveLocale($trackedKeywords);

        if ($project) {
            $chartRows = $project->keywordMetrics()
                ->selectRaw('date, SUM(clicks) as clicks, SUM(impressions) as impressions')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $latestSnapshots = $project->serpSnapshots()
                ->with(['trackedKeyword', 'results.competitor'])
                ->latest('captured_at')
                ->limit(60)
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

        $keywordRows = $this->keywordRows($project, $trackedKeywords);
        $keywordIntentFilter = request()->query('intent', 'all');
        $filteredKeywordRows = $keywordRows
            ->when($keywordIntentFilter !== 'all', fn (Collection $rows) => $rows->where('intent', $keywordIntentFilter))
            ->values();
        $quickWins = $keywordRows
            ->filter(fn ($row) => $row->avg_position >= 8 && $row->avg_position <= 20)
            ->sortByDesc('impressions')
            ->values();

        $competitors = $project
            ? $this->competitorsWithMetrics($project)
            : collect();
        $projectDomain = $this->projectDomain($project);
        $issueBuckets = $this->issueBuckets($latestCrawlPages);
        $priorityCrawlPage = $this->priorityCrawlPage($latestCrawlPages);
        $connectionStatus = $this->connectionStatus($project, $latestCrawlRun);
        $crawlSeveritySummary = $this->severitySummary($latestCrawlIssues);
        $latestSerpResults = $this->latestSerpResults($latestSerpSnapshot, $project, $competitors);
        $serpRows = $this->serpRows($trackedKeywords, $latestSnapshots, $project);
        $serpBucketFilter = request()->query('bucket', 'all');
        $filteredSerpRows = $serpRows
            ->when($serpBucketFilter === 'top3', fn (Collection $rows) => $rows->where('bucket', 'top3'))
            ->when($serpBucketFilter === 'top10', fn (Collection $rows) => $rows->whereIn('bucket', ['top3', 'top10']))
            ->when($serpBucketFilter === 'outside10', fn (Collection $rows) => $rows->where('bucket', 'outside10'))
            ->values();
        $serpOverview = $this->serpOverview($serpRows);
        $topFindings = $this->topFindings($issueBuckets, $latestCrawlPages);
        $auditActions = $this->auditActions($priorityCrawlPage, $topFindings, $latestAudit);
        $thematicReports = $this->thematicReports($latestCrawlRun, $latestCrawlPages);
        $summaryOrganic = $this->summaryOrganic($keywordRows, $topOrganicPages);
        $summaryHealth = $this->summaryHealth($latestCrawlRun, $latestCrawlPages, $crawlSeveritySummary);
        $summaryPerformance = $this->summaryPerformance($project, $latestAudit, $chartRows);

        return [
            'user' => $user,
            'project' => $project,
            'projectDomain' => $projectDomain,
            'workspaceLocale' => $locale,
            'latestAudit' => $latestAudit,
            'properties' => $properties,
            'propertyError' => $propertyError,
            'topKeywords' => $filteredKeywordRows,
            'keywordRows' => $keywordRows,
            'quickWins' => $quickWins,
            'competitors' => $competitors,
            'trackedKeywords' => $trackedKeywords,
            'latestSerpSnapshot' => $latestSerpSnapshot,
            'latestSerpResults' => $latestSerpResults,
            'latestSnapshots' => $latestSnapshots,
            'latestCrawlRun' => $latestCrawlRun,
            'latestCrawlPages' => $latestCrawlPages,
            'latestCrawlIssues' => $latestCrawlIssues->take(12)->values(),
            'priorityCrawlPage' => $priorityCrawlPage,
            'issueBuckets' => $issueBuckets,
            'topFindings' => $topFindings,
            'topCatalogPages' => $topCatalogPages,
            'topOrganicPages' => $topOrganicPages,
            'pageOpportunities' => collect($opportunities['page_opportunities']),
            'keywordOpportunities' => collect($opportunities['keyword_opportunities']),
            'competitorGaps' => collect($opportunities['competitor_gaps']),
            'trackedKeywordsByIntent' => $trackedKeywordsByIntent,
            'connectionStatus' => $connectionStatus,
            'serpOverview' => $serpOverview,
            'serpRows' => $filteredSerpRows,
            'auditActions' => $auditActions,
            'thematicReports' => $thematicReports,
            'summaryOrganic' => $summaryOrganic,
            'summaryHealth' => $summaryHealth,
            'summaryPerformance' => $summaryPerformance,
            'keywordIntentFilter' => $keywordIntentFilter,
            'serpBucketFilter' => $serpBucketFilter,
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
            'crawlSeveritySummary' => $crawlSeveritySummary,
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

    protected function resolveLocale(Collection $trackedKeywords): array
    {
        $countryCode = strtoupper((string) optional($trackedKeywords->first())->country_code ?: 'PA');
        $languageCode = strtolower((string) optional($trackedKeywords->first())->language_code ?: 'es');

        return [
            'country_code' => $countryCode,
            'country_label' => $this->countryLabel($countryCode),
            'language_code' => $languageCode,
            'language_label' => $this->languageLabel($languageCode),
            'market_label' => $countryCode.' · '.$languageCode.'-'.strtolower($countryCode),
        ];
    }

    protected function keywordRows(?Project $project, Collection $trackedKeywords): Collection
    {
        if (! $project) {
            return collect();
        }

        $trackedLookup = $trackedKeywords->keyBy(fn ($keyword) => Str::lower($keyword->keyword));

        return $project->keywordMetrics()
            ->select('keyword')
            ->selectRaw('SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(position) as avg_position')
            ->groupBy('keyword')
            ->orderByDesc('impressions')
            ->limit(50)
            ->get()
            ->map(function ($row) use ($trackedLookup) {
                $trackedKeyword = $trackedLookup->get(Str::lower($row->keyword));
                $intent = $trackedKeyword?->search_intent ?: $this->inferIntent($row->keyword);
                $ctr = $row->impressions > 0 ? ($row->clicks / $row->impressions) * 100 : 0;
                $difficulty = $this->difficultyScore((float) $row->avg_position, (int) $row->impressions, $ctr);
                $action = $this->keywordAction($intent, (float) $row->avg_position, $ctr, $difficulty);

                return (object) [
                    'keyword' => $row->keyword,
                    'intent' => $intent,
                    'intent_label' => $this->intentLabel($intent),
                    'clicks' => (int) $row->clicks,
                    'impressions' => (int) $row->impressions,
                    'avg_position' => round((float) $row->avg_position, 1),
                    'ctr' => round($ctr, 2),
                    'difficulty' => $difficulty,
                    'priority' => (int) ($trackedKeyword?->priority ?? 3),
                    'country_code' => strtoupper((string) ($trackedKeyword?->country_code ?? 'PA')),
                    'language_code' => strtolower((string) ($trackedKeyword?->language_code ?? 'es')),
                    'device' => $trackedKeyword?->device ?? 'desktop',
                    'action' => $action,
                ];
            })
            ->values();
    }

    protected function summaryOrganic(Collection $keywordRows, Collection $topOrganicPages): array
    {
        $bestKeyword = $keywordRows->sortBy('avg_position')->first();
        $growthKeyword = $keywordRows->sortByDesc('ctr')->first();
        $bestPage = $topOrganicPages->first();

        return [
            'best_keyword' => $bestKeyword,
            'growth_keyword' => $growthKeyword,
            'best_page' => $bestPage,
            'tracked_with_clicks' => $keywordRows->where('clicks', '>', 0)->count(),
        ];
    }

    protected function summaryHealth($latestCrawlRun, Collection $latestCrawlPages, array $crawlSeveritySummary): array
    {
        $indexableCount = $latestCrawlPages->where('is_indexable', true)->count();
        $sitemapCount = $latestCrawlPages->where('is_in_sitemap', true)->count();
        $pageCount = $latestCrawlPages->count();

        return [
            'pages_crawled' => (int) ($latestCrawlRun?->pages_crawled ?? 0),
            'indexable_rate' => $pageCount > 0 ? (int) round(($indexableCount / $pageCount) * 100) : 0,
            'sitemap_rate' => $pageCount > 0 ? (int) round(($sitemapCount / $pageCount) * 100) : 0,
            'critical' => $crawlSeveritySummary['error'] ?? 0,
            'warnings' => $crawlSeveritySummary['warn'] ?? 0,
        ];
    }

    protected function summaryPerformance(?Project $project, $latestAudit, Collection $chartRows): array
    {
        $clicks = (int) ($project?->keywordMetrics()->sum('clicks') ?? 0);
        $impressions = (int) ($project?->keywordMetrics()->sum('impressions') ?? 0);
        $ctr = $impressions > 0 ? round(($clicks / $impressions) * 100, 2) : 0;
        $dailyAverage = $chartRows->isNotEmpty() ? round($chartRows->avg('clicks'), 1) : 0;

        return [
            'performance_score' => (int) ($latestAudit?->performance_score ?? 0),
            'ctr' => $ctr,
            'daily_average_clicks' => $dailyAverage,
            'impressions_per_click' => $clicks > 0 ? round($impressions / $clicks, 1) : 0,
        ];
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
                'severity' => $issue['severity'] ?? $this->severityForIssueCode($issue['code'] ?? 'issue'),
            ]))
            ->groupBy('code')
            ->map(function (Collection $group) {
                $first = $group->first();

                return [
                    'code' => $first['code'],
                    'label' => $first['label'],
                    'severity' => $first['severity'],
                    'count' => $group->count(),
                    'action' => $this->issueAction($first['code']),
                ];
            })
            ->sortByDesc('count')
            ->values();
    }

    protected function topFindings(Collection $issueBuckets, Collection $pages): Collection
    {
        return $issueBuckets
            ->take(6)
            ->map(function (array $bucket) use ($pages) {
                $affectedPages = $pages
                    ->filter(fn ($page) => collect($page->issues ?? [])->contains(fn ($issue) => ($issue['code'] ?? null) === $bucket['code']))
                    ->take(3)
                    ->map(fn ($page) => parse_url((string) $page->url, PHP_URL_PATH) ?: $page->url)
                    ->values();

                return [
                    ...$bucket,
                    'affected_pages' => $affectedPages,
                ];
            });
    }

    protected function priorityCrawlPage(Collection $pages): ?array
    {
        $page = $pages
            ->sortByDesc(fn ($crawlPage) => count($crawlPage->issues ?? []))
            ->first();

        if (! $page) {
            return null;
        }

        $issues = collect($page->issues ?? []);

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
            'issues' => $issues,
            'recommendations' => $issues
                ->map(fn ($issue) => $this->issueAction($issue['code'] ?? 'issue'))
                ->unique()
                ->values(),
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

    protected function latestSerpResults($latestSerpSnapshot, ?Project $project, Collection $competitors): Collection
    {
        if (! $latestSerpSnapshot || ! $project) {
            return collect();
        }

        $allowedDomains = $competitors->pluck('domain')
            ->map(fn ($domain) => $this->normalizeDomain((string) $domain))
            ->push($this->normalizeDomain((string) parse_url((string) $project->url, PHP_URL_HOST)))
            ->filter()
            ->unique();

        return $latestSerpSnapshot->results
            ->sortBy('position')
            ->filter(function ($result) use ($allowedDomains) {
                $domain = $this->normalizeDomain((string) $result->domain);

                return $result->is_own_domain || $allowedDomains->contains($domain) || $this->isPanamanianDomain($domain);
            })
            ->values();
    }

    protected function serpRows(Collection $trackedKeywords, Collection $snapshots, ?Project $project): Collection
    {
        if (! $project) {
            return collect();
        }

        return $trackedKeywords
            ->map(function ($trackedKeyword) use ($snapshots, $project) {
                $history = $snapshots
                    ->where('tracked_keyword_id', $trackedKeyword->id)
                    ->sortByDesc('captured_at')
                    ->values();

                $current = $history->first();
                $previous = $history->get(1);
                $ownCurrent = $current?->results?->firstWhere('is_own_domain', true);
                $ownPrevious = $previous?->results?->firstWhere('is_own_domain', true);
                $currentPosition = $ownCurrent?->position;
                $previousPosition = $ownPrevious?->position;
                $bestCompetitor = collect($current?->results ?? [])
                    ->reject(fn ($result) => $result->is_own_domain)
                    ->filter(fn ($result) => $this->isPanamanianDomain($this->normalizeDomain((string) $result->domain)) || $result->competitor_id !== null)
                    ->sortBy('position')
                    ->first();
                $avgImpressions = (int) round($project->keywordMetrics()->where('keyword', $trackedKeyword->keyword)->avg('impressions') ?? 0);

                return (object) [
                    'keyword' => $trackedKeyword->keyword,
                    'position' => $currentPosition,
                    'previous_position' => $previousPosition,
                    'difference' => $currentPosition && $previousPosition ? $previousPosition - $currentPosition : null,
                    'volume' => $avgImpressions,
                    'bucket' => $currentPosition && $currentPosition <= 3
                        ? 'top3'
                        : ($currentPosition && $currentPosition <= 10 ? 'top10' : 'outside10'),
                    'best_competitor' => $bestCompetitor?->domain,
                    'best_competitor_position' => $bestCompetitor?->position,
                    'country_code' => strtoupper((string) $trackedKeyword->country_code),
                ];
            })
            ->sortBy([
                fn ($row) => $row->position ?? 999,
                fn ($row) => -1 * $row->volume,
            ])
            ->values();
    }

    protected function serpOverview(Collection $serpRows): array
    {
        return [
            'top3' => $serpRows->where('bucket', 'top3')->count(),
            'top10' => $serpRows->whereIn('bucket', ['top3', 'top10'])->count(),
            'outsideTop10' => $serpRows->where('bucket', 'outside10')->count(),
            'visibility' => $serpRows->isNotEmpty()
                ? round($serpRows->filter(fn ($row) => $row->position && $row->position <= 10)->count() / max($serpRows->count(), 1) * 100, 1)
                : 0,
        ];
    }

    protected function auditActions(?array $priorityCrawlPage, Collection $topFindings, $latestAudit): Collection
    {
        $actions = collect();

        if ($priorityCrawlPage) {
            $actions = $actions->merge($priorityCrawlPage['recommendations']->map(fn ($text) => [
                'title' => 'Corregir '.$priorityCrawlPage['path'],
                'body' => $text,
                'priority' => 'Alta',
            ]));
        }

        $actions = $actions->merge($topFindings->map(fn ($finding) => [
            'title' => $finding['label'],
            'body' => $finding['action'],
            'priority' => $finding['severity'] === 'error' ? 'Alta' : 'Media',
        ]));

        if (($latestAudit?->performance_score ?? 0) < 70) {
            $actions->prepend([
                'title' => 'Subir el rendimiento técnico',
                'body' => 'Reduce JavaScript no esencial, mejora LCP y comprime imágenes en las páginas con más tráfico orgánico.',
                'priority' => 'Alta',
            ]);
        }

        return $actions->unique('title')->take(6)->values();
    }

    protected function thematicReports($latestCrawlRun, Collection $latestCrawlPages): Collection
    {
        $pageCount = max($latestCrawlPages->count(), 1);
        $pagesWithIssue = $latestCrawlPages->filter(fn ($page) => count($page->issues ?? []) > 0)->count();
        $indexable = $latestCrawlPages->where('is_indexable', true)->count();
        $wellLinked = $latestCrawlPages->filter(fn ($page) => (int) $page->internal_links_count >= 5)->count();
        $described = $latestCrawlPages->filter(fn ($page) => filled($page->meta_description))->count();

        return collect([
            [
                'label' => 'Crawlability',
                'score' => $latestCrawlRun ? (int) round(($indexable / $pageCount) * 100) : 0,
                'hint' => 'Revisa indexación, sitemap y respuestas HTTP.',
            ],
            [
                'label' => 'Enlazado interno',
                'score' => $latestCrawlRun ? (int) round(($wellLinked / $pageCount) * 100) : 0,
                'hint' => 'Las páginas clave deben recibir más enlaces desde categorías y home.',
            ],
            [
                'label' => 'Metadatos',
                'score' => $latestCrawlRun ? (int) round(($described / $pageCount) * 100) : 0,
                'hint' => 'Titles, metas y H1 deben reforzar intención y cobertura semántica.',
            ],
            [
                'label' => 'Cobertura técnica',
                'score' => $latestCrawlRun ? (int) round((($pageCount - $pagesWithIssue) / $pageCount) * 100) : 0,
                'hint' => 'Mientras menos páginas con issues, más limpio el sitio para SEO.',
            ],
        ]);
    }

    protected function severityForIssueCode(string $code): string
    {
        return in_array($code, ['http_error', 'missing_title', 'missing_h1', 'unexpected_noindex', 'canonical_mismatch'], true)
            ? 'error'
            : 'warn';
    }

    protected function issueAction(string $code): string
    {
        return match ($code) {
            'http_error' => 'Corrige la URL para que responda 200, o redirígela con 301 si la página cambió.',
            'missing_title' => 'Escribe un title único con la keyword principal, la categoría y una propuesta de valor local para Panamá.',
            'missing_meta_description' => 'Agrega una meta description de 140-160 caracteres con beneficio, keyword y llamado a la acción.',
            'missing_h1' => 'Define un H1 claro y único alineado con la intención de búsqueda de la página.',
            'long_title' => 'Acorta el title para que no se corte en Google y coloca la keyword más importante al inicio.',
            'long_meta_description' => 'Reduce la meta description y deja la propuesta comercial más fuerte dentro de los primeros 150 caracteres.',
            'canonical_mismatch' => 'Revisa la canonical para que apunte a la URL indexable correcta y evites canibalización.',
            'unexpected_noindex' => 'Quita el noindex de esta página si debe posicionar y verifica robots/meta robots.',
            'images_missing_alt' => 'Completa atributos alt descriptivos en imágenes clave, incluyendo producto, marca o categoría.',
            'short_content' => 'Amplía el contenido con texto útil: beneficios, FAQs, marcas, cobertura y enlaces internos.',
            default => 'Revisa este hallazgo manualmente y priorízalo según impacto en indexación, CTR o contenido.',
        };
    }

    protected function inferIntent(string $keyword): string
    {
        $normalized = Str::lower($keyword);

        return match (true) {
            Str::contains($normalized, ['comprar', 'precio', 'oferta', 'delivery', 'envio']) => 'transactional',
            Str::contains($normalized, ['mejor', 'top', 'comparar', 'vs']) => 'commercial',
            Str::contains($normalized, ['como', 'que es', 'guia', 'receta', 'beneficios']) => 'informational',
            default => 'commercial',
        };
    }

    protected function keywordAction(string $intent, float $avgPosition, float $ctr, int $difficulty): string
    {
        if ($avgPosition <= 5 && $ctr < 2.5) {
            return 'Trabaja title y meta description para mejorar CTR sin cambiar la URL.';
        }

        if ($avgPosition > 10 && $difficulty <= 55) {
            return 'Refuerza contenido y enlazado interno para entrar al top 10 más rápido.';
        }

        if ($intent === 'transactional') {
            return 'Añade precio, disponibilidad, beneficios y señales de confianza cerca del primer scroll.';
        }

        return 'Expande la cobertura semántica y conecta esta keyword con categorías y páginas de apoyo.';
    }

    protected function difficultyScore(float $avgPosition, int $impressions, float $ctr): int
    {
        $score = (int) round(($avgPosition * 2.5) + (min($impressions, 5000) / 140) - ($ctr * 3));

        return max(18, min(88, $score));
    }

    protected function intentLabel(string $intent): string
    {
        return match ($intent) {
            'transactional' => 'Transaccional',
            'commercial' => 'Comercial',
            'navigational' => 'Navegacional',
            'informational' => 'Informativa',
            default => 'Sin clasificar',
        };
    }

    protected function countryLabel(string $countryCode): string
    {
        return match (strtoupper($countryCode)) {
            'PA' => 'Panamá',
            'US' => 'Estados Unidos',
            'MX' => 'México',
            'CO' => 'Colombia',
            default => strtoupper($countryCode),
        };
    }

    protected function languageLabel(string $languageCode): string
    {
        return match (strtolower($languageCode)) {
            'es' => 'Español',
            'en' => 'English',
            default => strtolower($languageCode),
        };
    }

    protected function normalizeDomain(string $domain): string
    {
        return Str::replaceFirst('www.', '', Str::lower($domain));
    }

    protected function isPanamanianDomain(string $domain): bool
    {
        return Str::endsWith($domain, '.pa');
    }
}
