<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class MagentoService
{
    public function syncCatalog(Project $project): array
    {
        if (config('seo.demo_mode') || ! $this->isConfigured($project)) {
            return $this->demoCatalog($project);
        }

        if (filled($project->magento_store_code)) {
            return $this->syncStore($project, $project->magento_store_code);
        }

        $storeCodes = $this->fetchActiveStoreCodes($project);

        return collect($storeCodes)
            ->flatMap(fn (string $code) => $this->syncStore($project, $code))
            ->all();
    }

    public function isConfigured(Project $project): bool
    {
        return filled($this->baseUrl($project)) && filled($this->token($project));
    }

    private function syncStore(Project $project, string $storeCode): array
    {
        return [
            ...$this->fetchProducts($project, $storeCode),
            ...$this->fetchCategories($project, $storeCode),
            ...$this->fetchCmsPages($project, $storeCode),
        ];
    }

    private function fetchActiveStoreCodes(Project $project): array
    {
        try {
            $stores = $this->client($project)
                ->get('/rest/V1/store/storeViews')
                ->throw()
                ->json();

            $codes = collect($stores)
                ->filter(fn ($s) => ($s['is_active'] ?? 0) && ($s['code'] ?? '') !== 'admin')
                ->pluck('code')
                ->filter()
                ->values()
                ->all();

            return $codes ?: ['default'];
        } catch (Throwable) {
            return ['default'];
        }
    }

    private function fetchProducts(Project $project, string $storeCode): array
    {
        $response = $this->client($project)->get($this->endpoint($storeCode, 'V1/products'), [
            'searchCriteria[currentPage]' => 1,
            'searchCriteria[pageSize]' => 100,
        ])->throw()->json();

        return collect($response['items'] ?? [])
            ->map(function (array $item) use ($project, $storeCode) {
                $slug = $this->attribute($item, 'url_key');
                $metaTitle = $this->attribute($item, 'meta_title');
                $metaDescription = $this->attribute($item, 'meta_description');
                $visibility = (int) ($this->attribute($item, 'visibility') ?? 4);

                return [
                    'project_id' => $project->id,
                    'type' => 'product',
                    'external_id' => $item['id'],
                    'url' => $this->pageUrl($project, $slug, 'product', $storeCode),
                    'slug' => $slug,
                    'name' => $item['name'] ?? 'Producto',
                    'status' => ((int) ($item['status'] ?? 1)) === 1 ? 'active' : 'disabled',
                    'meta_title' => $metaTitle ?: ($item['name'] ?? null),
                    'meta_description' => $metaDescription,
                    'canonical_url' => $this->pageUrl($project, $slug, 'product', $storeCode),
                    'is_indexable' => ((int) ($item['status'] ?? 1)) === 1 && $visibility !== 1,
                    'product_count' => 1,
                    'payload' => $item,
                ];
            })
            ->all();
    }

    private function fetchCategories(Project $project, string $storeCode): array
    {
        $response = $this->client($project)->get($this->endpoint($storeCode, 'V1/categories'))->throw()->json();

        return $this->flattenCategories($project, $response, $storeCode);
    }

    private function flattenCategories(Project $project, array $category, string $storeCode): array
    {
        $rows = [];

        if (isset($category['id']) && (int) $category['id'] > 1) {
            $slug = $category['custom_attributes']['url_path'] ?? $category['url_path'] ?? Str::slug($category['name'] ?? 'categoria');

            $rows[] = [
                'project_id' => $project->id,
                'type' => 'category',
                'external_id' => $category['id'],
                'url' => $this->pageUrl($project, $slug, 'category', $storeCode),
                'slug' => $slug,
                'name' => $category['name'] ?? 'Categoria',
                'status' => ((int) ($category['is_active'] ?? 1)) === 1 ? 'active' : 'disabled',
                'meta_title' => $category['meta_title'] ?? ($category['name'] ?? null),
                'meta_description' => $category['meta_description'] ?? null,
                'canonical_url' => $this->pageUrl($project, $slug, 'category', $storeCode),
                'is_indexable' => ((int) ($category['is_active'] ?? 1)) === 1,
                'product_count' => (int) ($category['product_count'] ?? 0),
                'payload' => $category,
            ];
        }

        foreach ($category['children_data'] ?? [] as $child) {
            $rows = [...$rows, ...$this->flattenCategories($project, $child, $storeCode)];
        }

        return $rows;
    }

    private function fetchCmsPages(Project $project, string $storeCode): array
    {
        $response = $this->client($project)->get($this->endpoint($storeCode, 'V1/cmsPage/search'), [
            'searchCriteria[currentPage]' => 1,
            'searchCriteria[pageSize]' => 100,
        ])->throw()->json();

        return collect($response['items'] ?? [])
            ->map(function (array $page) use ($project, $storeCode) {
                $slug = $page['identifier'] ?? null;

                return [
                    'project_id' => $project->id,
                    'type' => 'cms',
                    'external_id' => $page['id'],
                    'url' => $this->pageUrl($project, $slug, 'cms', $storeCode),
                    'slug' => $slug,
                    'name' => $page['title'] ?? 'CMS Page',
                    'status' => ((int) ($page['active'] ?? 1)) === 1 ? 'active' : 'disabled',
                    'meta_title' => $page['meta_title'] ?? ($page['title'] ?? null),
                    'meta_description' => $page['meta_description'] ?? null,
                    'canonical_url' => $this->pageUrl($project, $slug, 'cms', $storeCode),
                    'is_indexable' => ((int) ($page['active'] ?? 1)) === 1,
                    'product_count' => 0,
                    'payload' => $page,
                ];
            })
            ->all();
    }

    private function demoCatalog(Project $project): array
    {
        return [
            [
                'project_id' => $project->id,
                'type' => 'product',
                'external_id' => 1001,
                'url' => rtrim($project->url, '/').'/zapatillas-running-pro.html',
                'slug' => 'zapatillas-running-pro',
                'name' => 'Zapatillas Running Pro',
                'status' => 'active',
                'meta_title' => 'Zapatillas Running Pro para Hombre',
                'meta_description' => 'Modelo de alto rendimiento con envio rapido.',
                'canonical_url' => rtrim($project->url, '/').'/zapatillas-running-pro.html',
                'is_indexable' => true,
                'product_count' => 1,
                'payload' => ['source' => 'demo'],
            ],
            [
                'project_id' => $project->id,
                'type' => 'category',
                'external_id' => 2001,
                'url' => rtrim($project->url, '/').'/running-hombre',
                'slug' => 'running-hombre',
                'name' => 'Running Hombre',
                'status' => 'active',
                'meta_title' => 'Running Hombre',
                'meta_description' => 'Catalogo de running para hombre.',
                'canonical_url' => rtrim($project->url, '/').'/running-hombre',
                'is_indexable' => true,
                'product_count' => 24,
                'payload' => ['source' => 'demo'],
            ],
            [
                'project_id' => $project->id,
                'type' => 'cms',
                'external_id' => 3001,
                'url' => rtrim($project->url, '/').'/guia-de-tallas',
                'slug' => 'guia-de-tallas',
                'name' => 'Guia de Tallas',
                'status' => 'active',
                'meta_title' => 'Guia de Tallas',
                'meta_description' => 'Encuentra tu talla ideal.',
                'canonical_url' => rtrim($project->url, '/').'/guia-de-tallas',
                'is_indexable' => true,
                'product_count' => 0,
                'payload' => ['source' => 'demo'],
            ],
        ];
    }

    private function client(Project $project)
    {
        return Http::baseUrl($this->baseUrl($project))
            ->acceptJson()
            ->timeout((int) config('services.magento.timeout', 30))
            ->withToken($this->token($project))
            ->withOptions([
                'verify' => filter_var(config('services.magento.verify_ssl', true), FILTER_VALIDATE_BOOL),
            ]);
    }

    private function endpoint(string $storeCode, string $path): string
    {
        return '/rest/'.$storeCode.'/'.$path;
    }

    private function pageUrl(Project $project, ?string $slug, string $type, string $storeCode = 'default'): string
    {
        $base = rtrim($project->magento_base_url ?: $project->url, '/');

        if ($storeCode !== 'default') {
            $base .= '/'.$storeCode;
        }

        $slug = trim((string) $slug, '/');

        if ($slug === '') {
            return $base;
        }

        if ($type === 'product' && ! str_ends_with($slug, '.html')) {
            return $base.'/'.$slug.'.html';
        }

        return $base.'/'.$slug;
    }

    private function attribute(array $item, string $code): mixed
    {
        return collect($item['custom_attributes'] ?? [])
            ->firstWhere('attribute_code', $code)['value'] ?? null;
    }

    private function baseUrl(Project $project): string
    {
        return rtrim((string) ($project->magento_base_url ?: config('services.magento.base_url')), '/');
    }

    private function token(Project $project): string
    {
        return (string) ($project->magento_api_token ?: config('services.magento.token'));
    }
}
