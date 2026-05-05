<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MagentoService
{
    public function syncCatalog(Project $project): array
    {
        if (config('seo.demo_mode') || ! $this->isConfigured($project)) {
            return $this->demoCatalog($project);
        }

        return [
            ...$this->fetchProducts($project),
            ...$this->fetchCategories($project),
            ...$this->fetchCmsPages($project),
        ];
    }

    public function isConfigured(Project $project): bool
    {
        return filled($this->baseUrl($project)) && filled(config('services.magento.token'));
    }

    private function fetchProducts(Project $project): array
    {
        $response = $this->client($project)->get($this->endpoint($project, 'V1/products'), [
            'searchCriteria[currentPage]' => 1,
            'searchCriteria[pageSize]' => 100,
        ])->throw()->json();

        return collect($response['items'] ?? [])
            ->map(function (array $item) use ($project) {
                $slug = $this->attribute($item, 'url_key');
                $metaTitle = $this->attribute($item, 'meta_title');
                $metaDescription = $this->attribute($item, 'meta_description');
                $visibility = (int) ($this->attribute($item, 'visibility') ?? 4);

                return [
                    'project_id' => $project->id,
                    'type' => 'product',
                    'external_id' => $item['id'],
                    'url' => $this->pageUrl($project, $slug, 'product'),
                    'slug' => $slug,
                    'name' => $item['name'] ?? 'Producto',
                    'status' => ((int) ($item['status'] ?? 1)) === 1 ? 'active' : 'disabled',
                    'meta_title' => $metaTitle ?: ($item['name'] ?? null),
                    'meta_description' => $metaDescription,
                    'canonical_url' => $this->pageUrl($project, $slug, 'product'),
                    'is_indexable' => ((int) ($item['status'] ?? 1)) === 1 && $visibility !== 1,
                    'product_count' => 1,
                    'payload' => $item,
                ];
            })
            ->all();
    }

    private function fetchCategories(Project $project): array
    {
        $response = $this->client($project)->get($this->endpoint($project, 'V1/categories'))->throw()->json();

        return $this->flattenCategories($project, $response);
    }

    private function flattenCategories(Project $project, array $category): array
    {
        $rows = [];

        if (isset($category['id']) && (int) $category['id'] > 1) {
            $slug = $category['custom_attributes']['url_path'] ?? $category['url_path'] ?? Str::slug($category['name'] ?? 'categoria');

            $rows[] = [
                'project_id' => $project->id,
                'type' => 'category',
                'external_id' => $category['id'],
                'url' => $this->pageUrl($project, $slug, 'category'),
                'slug' => $slug,
                'name' => $category['name'] ?? 'Categoria',
                'status' => ((int) ($category['is_active'] ?? 1)) === 1 ? 'active' : 'disabled',
                'meta_title' => $category['meta_title'] ?? ($category['name'] ?? null),
                'meta_description' => $category['meta_description'] ?? null,
                'canonical_url' => $this->pageUrl($project, $slug, 'category'),
                'is_indexable' => ((int) ($category['is_active'] ?? 1)) === 1,
                'product_count' => (int) ($category['product_count'] ?? 0),
                'payload' => $category,
            ];
        }

        foreach ($category['children_data'] ?? [] as $child) {
            $rows = [...$rows, ...$this->flattenCategories($project, $child)];
        }

        return $rows;
    }

    private function fetchCmsPages(Project $project): array
    {
        $response = $this->client($project)->get($this->endpoint($project, 'V1/cmsPage/search'), [
            'searchCriteria[currentPage]' => 1,
            'searchCriteria[pageSize]' => 100,
        ])->throw()->json();

        return collect($response['items'] ?? [])
            ->map(function (array $page) use ($project) {
                $slug = $page['identifier'] ?? null;

                return [
                    'project_id' => $project->id,
                    'type' => 'cms',
                    'external_id' => $page['id'],
                    'url' => $this->pageUrl($project, $slug, 'cms'),
                    'slug' => $slug,
                    'name' => $page['title'] ?? 'CMS Page',
                    'status' => ((int) ($page['active'] ?? 1)) === 1 ? 'active' : 'disabled',
                    'meta_title' => $page['meta_title'] ?? ($page['title'] ?? null),
                    'meta_description' => $page['meta_description'] ?? null,
                    'canonical_url' => $this->pageUrl($project, $slug, 'cms'),
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
            ->withToken((string) config('services.magento.token'))
            ->withOptions([
                'verify' => filter_var(config('services.magento.verify_ssl', true), FILTER_VALIDATE_BOOL),
            ]);
    }

    private function endpoint(Project $project, string $path): string
    {
        return '/rest/'.$this->storeCode($project).'/'.$path;
    }

    private function pageUrl(Project $project, ?string $slug, string $type): string
    {
        $base = rtrim($project->magento_base_url ?: $project->url, '/');
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

    private function storeCode(Project $project): string
    {
        return (string) ($project->magento_store_code ?: config('services.magento.store_code', 'default'));
    }
}
