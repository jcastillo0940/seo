<?php

namespace App\Services;

use App\Models\Competitor;
use App\Models\Project;
use App\Models\TrackedKeyword;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class SerpTrackingService
{
    public function syncProject(Project $project): array
    {
        $keywords = $project->trackedKeywords()->orderBy('priority')->orderBy('keyword')->get();

        if ($keywords->isEmpty()) {
            return ['snapshots' => 0, 'results' => 0];
        }

        $snapshotCount = 0;
        $resultsCount = 0;

        foreach ($keywords as $trackedKeyword) {
            $payload = config('seo.demo_mode')
                ? $this->demoSnapshot($project, $trackedKeyword)
                : $this->liveSnapshot($project, $trackedKeyword);

            $snapshot = $project->serpSnapshots()->create([
                'tracked_keyword_id' => $trackedKeyword->id,
                'provider' => $payload['provider'],
                'search_engine' => 'google',
                'country_code' => $trackedKeyword->country_code,
                'language_code' => $trackedKeyword->language_code,
                'device' => $trackedKeyword->device,
                'results_count' => count($payload['results']),
                'captured_at' => now(),
                'raw_payload' => $payload['raw_payload'],
            ]);

            foreach ($payload['results'] as $result) {
                $competitor = $this->resolveCompetitor($project, $result['domain']);

                $snapshot->results()->create([
                    'competitor_id' => $competitor?->id,
                    'domain' => $result['domain'],
                    'url' => $result['url'],
                    'title' => $result['title'],
                    'position' => $result['position'],
                    'is_own_domain' => $result['is_own_domain'],
                    'estimated_ctr' => $this->estimateCtr($result['position']),
                    'estimated_traffic' => $this->estimateTraffic($project, $trackedKeyword, $result['position']),
                ]);

                if ($competitor) {
                    $competitor->forceFill(['last_seen_at' => now()])->save();
                }

                $resultsCount++;
            }

            $trackedKeyword->forceFill(['last_checked_at' => now()])->save();
            $snapshotCount++;
        }

        return [
            'snapshots' => $snapshotCount,
            'results' => $resultsCount,
        ];
    }

    private function liveSnapshot(Project $project, TrackedKeyword $trackedKeyword): array
    {
        $provider = config('services.serp.provider');

        if ($provider !== 'serpapi' || blank(config('services.serp.api_key'))) {
            throw new RuntimeException('Configura SERP provider y API key para tracking real de competencia.');
        }

        $response = Http::get('https://serpapi.com/search.json', [
            'engine' => 'google',
            'q' => $trackedKeyword->keyword,
            'api_key' => config('services.serp.api_key'),
            'google_domain' => 'google.com',
            'gl' => strtolower($trackedKeyword->country_code),
            'hl' => strtolower($trackedKeyword->language_code),
            'device' => $trackedKeyword->device,
            'num' => 10,
        ])->throw()->json();

        return [
            'provider' => 'serpapi',
            'raw_payload' => $response,
            'results' => collect($response['organic_results'] ?? [])
                ->take(10)
                ->map(fn (array $item) => [
                    'domain' => $this->hostFromUrl($item['link'] ?? ''),
                    'url' => $item['link'] ?? '',
                    'title' => $item['title'] ?? '',
                    'position' => (int) ($item['position'] ?? 0),
                    'is_own_domain' => $this->isOwnDomain($project, $item['link'] ?? ''),
                ])
                ->filter(fn (array $item) => $item['domain'] !== '')
                ->values()
                ->all(),
        ];
    }

    private function demoSnapshot(Project $project, TrackedKeyword $trackedKeyword): array
    {
        $ownHost = $this->hostFromUrl($project->url);
        $competitors = $project->competitors()->orderBy('id')->get();
        $domains = collect([$ownHost])
            ->merge($competitors->pluck('domain'))
            ->filter()
            ->unique()
            ->values();

        $results = $domains
            ->take(5)
            ->values()
            ->map(function (string $domain, int $index) use ($project, $trackedKeyword) {
                $position = $index + 1;
                $slug = Str::slug($trackedKeyword->keyword);
                $isOwn = $domain === $this->hostFromUrl($project->url);

                return [
                    'domain' => $domain,
                    'url' => 'https://'.$domain.'/'.$slug,
                    'title' => ($isOwn ? 'Tu tienda' : Str::headline(str_replace('.', ' ', $domain))).' | '.$trackedKeyword->keyword,
                    'position' => $position,
                    'is_own_domain' => $isOwn,
                ];
            })
            ->all();

        if (count($results) < 3) {
            $results[] = [
                'domain' => 'marketplace-demo.com',
                'url' => 'https://marketplace-demo.com/'.Str::slug($trackedKeyword->keyword),
                'title' => 'Marketplace Demo | '.$trackedKeyword->keyword,
                'position' => count($results) + 1,
                'is_own_domain' => false,
            ];
        }

        return [
            'provider' => 'demo',
            'raw_payload' => ['keyword' => $trackedKeyword->keyword, 'source' => 'demo'],
            'results' => $results,
        ];
    }

    private function resolveCompetitor(Project $project, string $domain): ?Competitor
    {
        return $project->competitors()->where('domain', $domain)->first();
    }

    private function estimateCtr(int $position): float
    {
        return match (true) {
            $position <= 1 => 0.34,
            $position === 2 => 0.17,
            $position === 3 => 0.11,
            $position <= 5 => 0.07,
            $position <= 10 => 0.03,
            default => 0.01,
        };
    }

    private function estimateTraffic(Project $project, TrackedKeyword $trackedKeyword, int $position): int
    {
        $volume = (int) round(
            $project->keywordMetrics()
                ->where('keyword', $trackedKeyword->keyword)
                ->avg('impressions') ?? (1200 / max($trackedKeyword->priority, 1))
        );

        return (int) round($volume * $this->estimateCtr($position));
    }

    private function isOwnDomain(Project $project, string $url): bool
    {
        return $this->hostFromUrl($url) === $this->hostFromUrl($project->url);
    }

    private function hostFromUrl(string $url): string
    {
        return Str::lower((string) parse_url($url, PHP_URL_HOST));
    }
}
