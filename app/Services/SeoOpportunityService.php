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
                $issues = collect($crawl?->issues ?? []);
                $issueCount = $issues->count();
                $sessions = (int) ($metric?->sessions ?? 0);
                $conversions = (int) ($metric?->conversions ?? 0);
                $conversionRate = $sessions > 0 ? round(($conversions / $sessions) * 100, 2) : 0;
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
                    'conversion_rate' => $conversionRate,
                    'issue_count' => $issueCount,
                    'top_issue' => $issues->first()['label'] ?? 'Sin issue tecnico fuerte',
                    'impact' => $sessions >= 100 ? 'Alto' : 'Medio',
                    'effort' => $issueCount >= 3 ? 'Medio' : 'Bajo',
                    'actions' => $this->pageActions($catalogPage->type, $issues->pluck('code')->all(), $sessions, $conversions),
                ];
            })
            ->sortByDesc('score')
            ->take(8)
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
                        ? 'Optimiza CTR con title, meta description y un ángulo comercial más claro.'
                        : 'Necesita mejor contenido y más enlaces internos para entrar al top 10.',
                ];
            });
    }

    private function competitorGaps(Project $project)
    {
        $latest = $project->serpSnapshots()->with(['results.competitor', 'trackedKeyword'])->latest('captured_at')->take(20)->get();

        return $latest
            ->flatMap(function ($snapshot) {
                $ownPosition = optional($snapshot->results->firstWhere('is_own_domain', true))->position ?? 99;

                return $snapshot->results
                    ->filter(fn ($result) => ! $result->is_own_domain
                        && $result->competitor_id !== null
                        && $result->position < $ownPosition
                    )
                    ->map(fn ($result) => [
                        'keyword' => $snapshot->trackedKeyword?->keyword,
                        'competitor' => $result->competitor->name,
                        'position' => $result->position,
                        'own_position' => $ownPosition,
                        'gap' => max(0, $ownPosition - $result->position),
                    ]);
            })
            ->filter(fn ($row) => filled($row['keyword']))
            ->sortByDesc('gap')
            ->take(6)
            ->values();
    }

    private function pageActions(string $type, array $issueCodes, int $sessions, int $conversions): array
    {
        $actions = collect();

        if (in_array('missing_title', $issueCodes, true)) {
            $actions->push('Crear un title único con keyword principal, categoría y atributo comercial.');
        }

        if (in_array('missing_meta_description', $issueCodes, true)) {
            $actions->push('Escribir una meta description que resuma beneficio, surtido y CTA.');
        }

        if (in_array('missing_h1', $issueCodes, true)) {
            $actions->push('Agregar un H1 alineado con la intención de búsqueda real de la página.');
        }

        if (in_array('short_content', $issueCodes, true)) {
            $actions->push('Expandir el contenido con FAQs, marcas, usos, beneficios y enlazado interno.');
        }

        if (in_array('images_missing_alt', $issueCodes, true)) {
            $actions->push('Completar atributos alt descriptivos en imágenes clave de producto o categoría.');
        }

        if ($sessions > 0 && $conversions === 0) {
            $actions->push('Reforzar intención transaccional con precio, disponibilidad y señales de confianza.');
        }

        if ($type === 'category') {
            $actions->push('Enlazar esta categoría desde home, menú y categorías relacionadas para subir autoridad interna.');
        }

        return $actions
            ->filter()
            ->unique()
            ->take(4)
            ->values()
            ->all();
    }

    private function pathFromUrl(?string $url): string
    {
        return Str::of((string) parse_url((string) $url, PHP_URL_PATH))->rtrim('/')->value() ?: '/';
    }
}
