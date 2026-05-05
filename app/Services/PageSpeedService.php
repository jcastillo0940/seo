<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Http;

class PageSpeedService
{
    public function run(Project $project): array
    {
        if (config('seo.demo_mode') || blank(config('services.google.pagespeed_api_key'))) {
            return [
                'performance_score' => 84,
                'seo_score' => 91,
                'raw_data' => [
                    'source' => 'demo',
                    'loadingExperience' => [
                        'overall_category' => 'FAST',
                    ],
                ],
            ];
        }

        $response = Http::get('https://www.googleapis.com/pagespeedonline/v5/runPagespeed', [
            'url' => $project->url,
            'category' => ['performance', 'seo'],
            'strategy' => 'mobile',
            'key' => config('services.google.pagespeed_api_key'),
        ])->throw()->json();

        $categories = $response['lighthouseResult']['categories'] ?? [];

        return [
            'performance_score' => (int) round((($categories['performance']['score'] ?? 0) * 100)),
            'seo_score' => (int) round((($categories['seo']['score'] ?? 0) * 100)),
            'raw_data' => $response,
        ];
    }
}
