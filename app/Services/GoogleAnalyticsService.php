<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GoogleAnalyticsService
{
    public function __construct(
        private readonly GoogleApiClientFactory $clientFactory,
    ) {
    }

    public function fetchOrganicLandingPages(Project $project, Carbon $startDate, Carbon $endDate): array
    {
        if (config('seo.demo_mode')) {
            return $this->demoMetrics($startDate, $endDate);
        }

        if (blank($project->ga4_property_id)) {
            throw new RuntimeException('Este proyecto no tiene GA4 Property ID configurado.');
        }

        $client = $this->clientFactory->make($project->user, ['https://www.googleapis.com/auth/analytics.readonly']);
        $token = $client->getAccessToken()['access_token'] ?? null;

        if (! $token) {
            throw new RuntimeException('No hay token valido de Google para consultar GA4.');
        }

        $response = Http::withToken($token)
            ->post('https://analyticsdata.googleapis.com/v1beta/properties/'.$project->ga4_property_id.':runReport', [
                'dateRanges' => [[
                    'startDate' => $startDate->toDateString(),
                    'endDate' => $endDate->toDateString(),
                ]],
                'dimensions' => [
                    ['name' => 'date'],
                    ['name' => 'pagePath'],
                    ['name' => 'pageTitle'],
                    ['name' => 'sessionDefaultChannelGroup'],
                ],
                'metrics' => [
                    ['name' => 'sessions'],
                    ['name' => 'totalUsers'],
                    ['name' => 'conversions'],
                ],
                'dimensionFilter' => [
                    'filter' => [
                        'fieldName' => 'sessionDefaultChannelGroup',
                        'stringFilter' => [
                            'value' => 'Organic Search',
                            'matchType' => 'EXACT',
                        ],
                    ],
                ],
                'limit' => 1000,
            ])
            ->throw()
            ->json();

        return collect($response['rows'] ?? [])
            ->map(function (array $row) {
                return [
                    'date' => Carbon::createFromFormat('Ymd', $row['dimensionValues'][0]['value'] ?? now()->format('Ymd'))->toDateString(),
                    'page_path' => $row['dimensionValues'][1]['value'] ?? '/',
                    'page_title' => $row['dimensionValues'][2]['value'] ?? null,
                    'channel_group' => $row['dimensionValues'][3]['value'] ?? 'Organic Search',
                    'sessions' => (int) ($row['metricValues'][0]['value'] ?? 0),
                    'users' => (int) ($row['metricValues'][1]['value'] ?? 0),
                    'conversions' => (int) round((float) ($row['metricValues'][2]['value'] ?? 0)),
                ];
            })
            ->all();
    }

    private function demoMetrics(Carbon $startDate, Carbon $endDate): array
    {
        return [
            [
                'date' => $endDate->toDateString(),
                'page_path' => '/running-hombre',
                'page_title' => 'Running Hombre',
                'channel_group' => 'Organic Search',
                'sessions' => 184,
                'users' => 156,
                'conversions' => 9,
            ],
            [
                'date' => $endDate->toDateString(),
                'page_path' => '/zapatillas-running-pro.html',
                'page_title' => 'Zapatillas Running Pro',
                'channel_group' => 'Organic Search',
                'sessions' => 132,
                'users' => 111,
                'conversions' => 6,
            ],
        ];
    }
}
