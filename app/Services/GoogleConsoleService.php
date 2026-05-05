<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GoogleConsoleService
{
    public function __construct(
        private readonly GoogleApiClientFactory $clientFactory,
    ) {
    }

    public function listProperties(User $user): array
    {
        if (config('seo.demo_mode')) {
            return [
                ['property_id' => 'sc-domain:midominio.com', 'url' => 'https://midominio.com', 'name' => 'Mi Dominio', 'type' => 'sc-domain'],
                ['property_id' => 'sc-domain:blog.midominio.com', 'url' => 'https://blog.midominio.com', 'name' => 'Blog', 'type' => 'sc-domain'],
            ];
        }

        if (! class_exists(\Google\Service\Webmasters::class)) {
            throw new RuntimeException('Laravel Socialite y google/apiclient deben instalarse para acceder a Search Console.');
        }

        $client = $this->clientFactory->make($user, ['https://www.googleapis.com/auth/webmasters.readonly']);
        $service = new \Google\Service\Webmasters($client);
        $sites = $service->sites->listSites()->getSiteEntry() ?? [];

        return collect($sites)
            ->map(function ($site) {
                $siteUrl = $site->getSiteUrl();
                $isDomain = str_starts_with($siteUrl, 'sc-domain:');
                $domain = Str::of($siteUrl)->replace(['sc-domain:', 'https://', 'http://'], '')->trim('/')->value();

                return [
                    'property_id' => $siteUrl,
                    'url' => $isDomain ? 'https://'.$domain : $siteUrl,
                    'name' => $domain,
                    'type' => $isDomain ? 'sc-domain' : 'url-prefix',
                ];
            })
            ->values()
            ->all();
    }

    public function fetchKeywordMetrics(Project $project, Carbon $startDate, Carbon $endDate): array
    {
        if (config('seo.demo_mode')) {
            return $this->demoMetrics($startDate, $endDate);
        }

        if (! class_exists(\Google\Service\Webmasters::class)) {
            throw new RuntimeException('google/apiclient es necesario para sincronizar Search Console.');
        }

        $client = $this->clientFactory->make($project->user, ['https://www.googleapis.com/auth/webmasters.readonly']);
        $token = $client->getAccessToken()['access_token'] ?? null;

        if (! $token) {
            throw new RuntimeException('No hay token valido de Google para este usuario.');
        }

        $response = Http::withToken($token)
            ->post('https://searchconsole.googleapis.com/webmasters/v3/sites/'.urlencode($project->google_property_id).'/searchAnalytics/query', [
                'startDate' => $startDate->toDateString(),
                'endDate' => $endDate->toDateString(),
                'dimensions' => ['query', 'date'],
                'rowLimit' => 25000,
            ])
            ->throw()
            ->json();

        return collect($response['rows'] ?? [])
            ->map(function (array $row) {
                return [
                    'keyword' => $row['keys'][0] ?? 'unknown',
                    'date' => $row['keys'][1] ?? now()->toDateString(),
                    'clicks' => (int) round($row['clicks'] ?? 0),
                    'impressions' => (int) round($row['impressions'] ?? 0),
                    'position' => round((float) ($row['position'] ?? 0), 2),
                ];
            })
            ->all();
    }

    private function demoMetrics(Carbon $startDate, Carbon $endDate): array
    {
        $keywords = [
            ['seo tool para pymes', 12.4, 41, 1120],
            ['auditoria seo tecnica', 8.1, 79, 1420],
            ['google search console laravel', 17.9, 18, 980],
            ['page speed insights dashboard', 14.3, 24, 1260],
            ['mejorar core web vitals', 6.8, 63, 870],
            ['keywords con impresiones altas', 11.6, 29, 1650],
        ];

        $rows = [];

        foreach ($keywords as [$keyword, $position, $clicks, $impressions]) {
            foreach (range(0, $startDate->diffInDays($endDate)) as $offset) {
                $date = $startDate->copy()->addDays($offset);

                $rows[] = [
                    'keyword' => $keyword,
                    'date' => $date->toDateString(),
                    'clicks' => max(0, $clicks + random_int(-5, 5)),
                    'impressions' => max(50, $impressions + random_int(-120, 120)),
                    'position' => max(1, round($position + (random_int(-10, 10) / 10), 2)),
                ];
            }
        }

        return $rows;
    }
}
