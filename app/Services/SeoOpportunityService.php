<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Str;

class SeoOpportunityService
{
    public function forProject(Project $project): array
    {
        return [
            'page_opportunities' => $this->pageOpportunities($project),
            'keyword_opportunities' => $this->keywordOpportunities($project),
            'competitor_gaps' => $this->competitorGaps($project),
        ];
    }

    private function pageOpportunities(Project $project)
    {
        $latestCrawlRun = $project->crawlRuns()->latest('finished_at')->first();
        $crawlPages = $latestCrawlRun?->pages()->get()->keyBy(fn ($page) => $this->pathFromUrl($page->url)) ?? collect();
        $analytics = $project->analyticsPageMetrics()
            ->select('page_path', 'page_title')
            ->selectRaw('SUM(sessions) as sessions, SUM(conversions) as conversions')
            ->groupBy('page_path', 'page_title')
            ->get()
            ->keyBy('page_path');

        return $project->catalogPages()
            ->get()
            ->map(function ($catalogPage) use ($crawlPages, $analytics) {
                $path = $this->pathFromUrl($catalogPage->url);
                $crawl = $crawlPages->get($path);
                $metric = $analytics->get($path);
                $issueCount = count($crawl?->issues ?? []);
                $sessions = (int) ($metric?->sessions ?? 0);
                $conversions = (int) ($metric?->conversions ?? 0);

                $score = ($sessions * 0.45)
                    + ($catalogPage->product_count * 2)
                    + ($issueCount * 18)
                    + (max(0, 5 - $conversions) * 8);

                return [
                    'name' => $catalogPage->name,
                    'url' => $catalogPage->url,
                    'type' => $catalogPage->type,
                    'score' => (int) round($score),
                    'sessions' => $sessions,
                    'conversions' => $conversions,
                    'issue_count' => $issueCount,
                    'top_issue' => $crawl?->issues[0]['label'] ?? 'Sin issues tecnicos fuertes',
                ];
            })
            ->sortByDesc('score')
            ->take(6)
            ->values();
    }

    private function keywordOpportunities(Project $project)
    {
        return $project->keywordMetrics()
            ->select('keyword')
            ->selectRaw('SUM(impressions) as impressions, SUM(clicks) as clicks, AVG(position) as avg_position')
            ->groupBy('keyword')
            ->havingRaw('AVG(position) BETWEEN 8 AND 20')
            ->orderByDesc('impressions')
            ->limit(6)
            ->get()
            ->map(function ($row) {
                $ctr = $row->impressions > 0 ? ($row->clicks / $row->impressions) * 100 : 0;

                return [
                    'keyword' => $row->keyword,
                    'avg_position' => round((float) $row->avg_position, 1),
                    'impressions' => (int) $row->impressions,
                    'clicks' => (int) $row->clicks,
                    'ctr' => round($ctr, 2),
                    'hint' => $row->avg_position <= 10
                        ? 'Optimiza CTR con title y meta description.'
                        : 'Necesita mejor contenido o enlaces internos para entrar al top 10.',
                ];
            });
    }

    private function competitorGaps(Project $project)
    {
        $latest = $project->serpSnapshots()->with('results.competitor')->latest('captured_at')->take(5)->get();

        return $latest
            ->flatMap(function ($snapshot) {
                $ownPosition = optional($snapshot->results->firstWhere('is_own_domain', true))->position ?? 99;

                return $snapshot->results
                    ->filter(fn ($result) => ! $result->is_own_domain && $result->position < $ownPosition)
                    ->map(fn ($result) => [
                        'keyword' => $snapshot->trackedKeyword->keyword,
                        'competitor' => $result->competitor?->name ?: $result->domain,
                        'position' => $result->position,
                        'own_position' => $ownPosition,
                        'gap' => max(0, $ownPosition - $result->position),
                    ]);
            })
            ->sortByDesc('gap')
            ->take(6)
            ->values();
    }

    private function pathFromUrl(?string $url): string
    {
        return Str::of((string) parse_url((string) $url, PHP_URL_PATH))->rtrim('/')->value() ?: '/';
    }
}
