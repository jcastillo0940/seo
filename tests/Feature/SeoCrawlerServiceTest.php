<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Services\SeoCrawlerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoCrawlerServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_crawler_returns_issue_summary_for_catalog_pages(): void
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

        $project->catalogPages()->create([
            'type' => 'product',
            'external_id' => 1001,
            'url' => 'https://midominio.com/zapatillas-running-pro.html',
            'slug' => 'zapatillas-running-pro',
            'name' => 'Zapatillas Running Pro',
            'status' => 'active',
            'is_indexable' => true,
        ]);

        $project->catalogPages()->create([
            'type' => 'category',
            'external_id' => 2001,
            'url' => 'https://midominio.com/running-hombre',
            'slug' => 'running-hombre',
            'name' => 'Running Hombre',
            'status' => 'active',
            'is_indexable' => true,
        ]);

        $result = app(SeoCrawlerService::class)->crawl($project);

        $this->assertSame('completed', $result['status']);
        $this->assertCount(2, $result['pages']);
        $this->assertGreaterThan(0, $result['issue_count']);
        $this->assertGreaterThan(0, $result['summary']['pages_with_issues']);
        $this->assertGreaterThan(0, $result['summary']['images_without_alt']);
    }
}
