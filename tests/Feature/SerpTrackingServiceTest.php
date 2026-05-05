<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Services\SerpTrackingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SerpTrackingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_serp_tracking_creates_results_for_tracked_keywords(): void
    {
        config()->set('seo.demo_mode', true);

        $user = User::factory()->create();
        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'Mi Dominio',
            'url' => 'https://midominio.com',
            'google_property_id' => 'sc-domain:midominio.com',
            'google_property_type' => 'sc-domain',
        ]);

        $project->competitors()->create([
            'domain' => 'competidor.com',
            'name' => 'Competidor',
        ]);

        $project->trackedKeywords()->create([
            'keyword' => 'zapatillas running',
            'country_code' => 'US',
            'language_code' => 'es',
            'device' => 'mobile',
            'priority' => 2,
            'source' => 'manual',
        ]);

        $summary = app(SerpTrackingService::class)->syncProject($project);

        $this->assertSame(1, $summary['snapshots']);
        $this->assertGreaterThan(0, $summary['results']);
        $this->assertDatabaseCount('serp_snapshots', 1);
        $this->assertDatabaseCount('serp_results', $summary['results']);
    }
}
