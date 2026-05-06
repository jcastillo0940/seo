<?php

namespace Tests\Feature;

use App\Models\KeywordMetric;
use App\Models\Project;
use App\Models\TechnicalAudit;
use App\Models\TrackedKeyword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_login_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Inicia sesion');
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    }

    public function test_authenticated_user_sees_dashboard_metrics(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'Mi Dominio',
            'url' => 'https://midominio.com',
            'google_property_id' => 'sc-domain:midominio.com',
            'google_property_type' => 'sc-domain',
            'magento_base_url' => 'https://shop.midominio.com',
            'magento_store_code' => 'default',
            'ga4_property_id' => '123456789',
        ]);

        KeywordMetric::create([
            'project_id' => $project->id,
            'keyword' => 'auditoria seo tecnica',
            'position' => 12.8,
            'clicks' => 55,
            'impressions' => 1200,
            'date' => now()->toDateString(),
        ]);

        TechnicalAudit::create([
            'project_id' => $project->id,
            'performance_score' => 84,
            'seo_score' => 91,
            'json_raw_data' => ['source' => 'test'],
            'audited_at' => now(),
        ]);

        $project->competitors()->create([
            'domain' => 'competidor.com',
            'name' => 'Competidor',
        ]);

        TrackedKeyword::create([
            'project_id' => $project->id,
            'keyword' => 'zapatillas running',
            'country_code' => 'US',
            'language_code' => 'es',
            'device' => 'mobile',
            'priority' => 2,
            'source' => 'manual',
        ]);

        $project->catalogPages()->create([
            'type' => 'category',
            'external_id' => 2001,
            'url' => 'https://shop.midominio.com/running-hombre',
            'slug' => 'running-hombre',
            'name' => 'Running Hombre',
            'status' => 'active',
            'product_count' => 24,
        ]);

        $project->analyticsPageMetrics()->create([
            'date' => now()->toDateString(),
            'page_path' => '/running-hombre',
            'page_title' => 'Running Hombre',
            'sessions' => 184,
            'users' => 156,
            'conversions' => 9,
            'channel_group' => 'Organic Search',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Mi Dominio')
            ->assertSee('auditoria seo tecnica')
            ->assertSee('84')
            ->assertSee('Competidor')
            ->assertSee('zapatillas running')
            ->assertSee('Catalogo Magento')
            ->assertSee('Landing pages organicas');
    }

    public function test_authenticated_user_can_store_competitor(): void
    {
        $user = User::factory()->create();

        Project::create([
            'user_id' => $user->id,
            'name' => 'Mi Dominio',
            'url' => 'https://midominio.com',
            'google_property_id' => 'sc-domain:midominio.com',
            'google_property_type' => 'sc-domain',
        ]);

        $this->actingAs($user)
            ->post('/competitors', [
                'domain' => 'https://competidor.com/categoria',
                'name' => 'Competidor X',
                'notes' => 'Top rival',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('competitors', [
            'domain' => 'competidor.com',
            'name' => 'Competidor X',
        ]);
    }

    public function test_authenticated_user_can_store_tracked_keyword(): void
    {
        $user = User::factory()->create();

        Project::create([
            'user_id' => $user->id,
            'name' => 'Mi Dominio',
            'url' => 'https://midominio.com',
            'google_property_id' => 'sc-domain:midominio.com',
            'google_property_type' => 'sc-domain',
        ]);

        $this->actingAs($user)
            ->post('/tracked-keywords', [
                'keyword' => 'comprar tenis online',
                'country_code' => 'us',
                'language_code' => 'es',
                'device' => 'mobile',
                'search_intent' => 'transactional',
                'priority' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tracked_keywords', [
            'keyword' => 'comprar tenis online',
            'country_code' => 'US',
            'language_code' => 'es',
            'device' => 'mobile',
            'priority' => 1,
        ]);
    }

    public function test_authenticated_user_can_update_project_settings(): void
    {
        $user = User::factory()->create();

        Project::create([
            'user_id' => $user->id,
            'name' => 'Mi Dominio',
            'url' => 'https://midominio.com',
            'google_property_id' => 'sc-domain:midominio.com',
            'google_property_type' => 'sc-domain',
        ]);

        $this->actingAs($user)
            ->post('/project/settings', [
                'magento_base_url' => 'https://shop.midominio.com',
                'magento_store_code' => 'default',
                'magento_website_code' => 'base',
                'ga4_property_id' => '123456789',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'magento_base_url' => 'https://shop.midominio.com',
            'magento_store_code' => 'default',
            'magento_website_code' => 'base',
            'ga4_property_id' => '123456789',
        ]);
    }

    public function test_authenticated_user_can_queue_seo_crawl(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        Project::create([
            'user_id' => $user->id,
            'name' => 'Mi Dominio',
            'url' => 'https://midominio.com',
            'google_property_id' => 'sc-domain:midominio.com',
            'google_property_type' => 'sc-domain',
        ]);

        $this->actingAs($user)
            ->post('/project/run-crawl')
            ->assertRedirect();

        Queue::assertPushed(\App\Jobs\RunSeoCrawl::class);
    }

    public function test_authenticated_user_can_open_all_workspace_views(): void
    {
        $user = User::factory()->create();

        Project::create([
            'user_id' => $user->id,
            'name' => 'Mi Dominio',
            'url' => 'https://midominio.com',
            'google_property_id' => 'sc-domain:midominio.com',
            'google_property_type' => 'sc-domain',
        ]);

        $pages = [
            '/resumen' => 'Command Center',
            '/deep-scan' => 'Deep Scan',
            '/keyword-hunter' => 'Keyword Hunter',
            '/serp-tracking' => 'SERP Tracking',
            '/competidores' => 'Competidores',
            '/conexiones' => 'Conexiones del proyecto',
            '/oportunidades' => 'Oportunidades',
            '/auditoria' => 'Auditoria',
        ];

        foreach ($pages as $url => $expectedText) {
            $this->actingAs($user)
                ->get($url)
                ->assertOk()
                ->assertSee($expectedText);
        }
    }

    public function test_unauthorized_google_user_is_rejected(): void
    {
        config()->set('seo.demo_mode', false);
        config()->set('auth.access.allowed_emails', ['allowed@example.com']);
        config()->set('auth.access.allowed_domains', []);
        config()->set('auth.access.require_allowlist', true);

        $socialiteUser = Mockery::mock(SocialiteUserContract::class);
        $socialiteUser->shouldReceive('getEmail')->andReturn('blocked@example.com');

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        $this->get('/auth/google/callback')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
