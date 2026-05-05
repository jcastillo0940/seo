<?php

namespace App\Services;

use App\Models\CatalogPage;
use App\Models\Project;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SeoCrawlerService
{
    public function crawl(Project $project): array
    {
        $pages = $project->catalogPages()->orderBy('type')->orderBy('id')->get();

        if ($pages->isEmpty()) {
            return [
                'status' => 'completed',
                'pages_crawled' => 0,
                'issue_count' => 0,
                'summary' => [
                    'indexable_pages' => 0,
                    'pages_with_issues' => 0,
                    'missing_titles' => 0,
                    'missing_descriptions' => 0,
                    'missing_h1' => 0,
                    'images_without_alt' => 0,
                ],
                'pages' => [],
            ];
        }

        $rows = config('seo.demo_mode')
            ? $this->demoCrawl($project, $pages->all())
            : $this->liveCrawl($project, $pages->all());

        $issueCount = collect($rows)->sum(fn (array $row) => count($row['issues'] ?? []));

        return [
            'status' => 'completed',
            'pages_crawled' => count($rows),
            'issue_count' => $issueCount,
            'summary' => [
                'indexable_pages' => collect($rows)->where('is_indexable', true)->count(),
                'pages_with_issues' => collect($rows)->filter(fn (array $row) => ! empty($row['issues']))->count(),
                'missing_titles' => collect($rows)->filter(fn (array $row) => $this->hasIssue($row, 'missing_title'))->count(),
                'missing_descriptions' => collect($rows)->filter(fn (array $row) => $this->hasIssue($row, 'missing_meta_description'))->count(),
                'missing_h1' => collect($rows)->filter(fn (array $row) => $this->hasIssue($row, 'missing_h1'))->count(),
                'images_without_alt' => collect($rows)->sum('images_without_alt_count'),
            ],
            'pages' => $rows,
        ];
    }

    private function liveCrawl(Project $project, array $pages): array
    {
        return collect($pages)
            ->map(fn (CatalogPage $page) => $this->inspectLivePage($project, $page))
            ->all();
    }

    private function inspectLivePage(Project $project, CatalogPage $catalogPage): array
    {
        $response = Http::timeout((int) config('seo.crawler.timeout', 20))
            ->withOptions([
                'allow_redirects' => true,
                'verify' => filter_var(config('seo.crawler.verify_ssl', true), FILTER_VALIDATE_BOOL),
            ])
            ->get($catalogPage->url);

        $html = (string) $response->body();
        $parsed = $this->parseHtml($html);
        $title = $this->extractTitle($parsed['xpath']);
        $metaDescription = $this->extractMetaDescription($parsed['xpath']);
        $h1 = $this->extractH1($parsed['xpath']);
        $canonical = $this->extractCanonical($parsed['xpath']);
        $robots = $this->extractRobots($parsed['xpath']);
        $imageAltMissing = $this->countImagesWithoutAlt($parsed['xpath']);
        $internalLinksCount = $this->countInternalLinks($parsed['xpath'], $project);
        $wordCount = str_word_count(strip_tags($html));
        $issues = $this->buildIssues(
            $response->status(),
            $title,
            $metaDescription,
            $h1,
            $canonical,
            $robots,
            $imageAltMissing,
            $wordCount,
            $catalogPage
        );

        return [
            'project_id' => $project->id,
            'url' => $catalogPage->url,
            'status_code' => $response->status(),
            'title' => $title,
            'meta_description' => $metaDescription,
            'h1' => $h1,
            'canonical_url' => $canonical,
            'robots_directives' => $robots,
            'is_indexable' => ! Str::contains(Str::lower((string) $robots), 'noindex'),
            'is_in_sitemap' => true,
            'internal_links_count' => $internalLinksCount,
            'images_without_alt_count' => $imageAltMissing,
            'word_count' => $wordCount,
            'issues' => $issues,
        ];
    }

    private function demoCrawl(Project $project, array $pages): array
    {
        return collect($pages)
            ->map(function (CatalogPage $page, int $index) use ($project) {
                $issues = [];

                if ($page->type === 'category') {
                    $issues[] = ['code' => 'short_content', 'label' => 'Contenido escaso en categoria'];
                }

                if ($page->type === 'product') {
                    $issues[] = ['code' => 'missing_meta_description', 'label' => 'Meta description ausente'];
                    $issues[] = ['code' => 'images_missing_alt', 'label' => 'Imagenes sin atributo alt'];
                }

                if ($page->type === 'cms' && $index % 2 === 0) {
                    $issues[] = ['code' => 'missing_h1', 'label' => 'H1 ausente'];
                }

                return [
                    'project_id' => $project->id,
                    'url' => $page->url,
                    'status_code' => 200,
                    'title' => $page->meta_title ?: $page->name,
                    'meta_description' => $page->type === 'product' ? null : ($page->meta_description ?: 'Contenido de apoyo para SEO.'),
                    'h1' => $page->type === 'cms' && $index % 2 === 0 ? null : $page->name,
                    'canonical_url' => $page->canonical_url ?: $page->url,
                    'robots_directives' => $page->is_indexable ? 'index,follow' : 'noindex,follow',
                    'is_indexable' => $page->is_indexable,
                    'is_in_sitemap' => true,
                    'internal_links_count' => $page->type === 'category' ? 18 : 6,
                    'images_without_alt_count' => $page->type === 'product' ? 2 : 0,
                    'word_count' => $page->type === 'category' ? 85 : 220,
                    'issues' => $issues,
                ];
            })
            ->all();
    }

    private function parseHtml(string $html): array
    {
        $document = new DOMDocument();

        libxml_use_internal_errors(true);
        $document->loadHTML($html !== '' ? $html : '<html></html>');
        libxml_clear_errors();

        return [
            'document' => $document,
            'xpath' => new DOMXPath($document),
        ];
    }

    private function extractTitle(DOMXPath $xpath): ?string
    {
        return $this->firstNodeValue($xpath, '//title');
    }

    private function extractMetaDescription(DOMXPath $xpath): ?string
    {
        return $this->metaContent($xpath, 'description');
    }

    private function extractH1(DOMXPath $xpath): ?string
    {
        return $this->firstNodeValue($xpath, '//h1');
    }

    private function extractCanonical(DOMXPath $xpath): ?string
    {
        $node = $xpath->query("//link[translate(@rel, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')='canonical']")->item(0);

        return $node?->attributes?->getNamedItem('href')?->nodeValue;
    }

    private function extractRobots(DOMXPath $xpath): ?string
    {
        return $this->metaContent($xpath, 'robots');
    }

    private function countImagesWithoutAlt(DOMXPath $xpath): int
    {
        $images = $xpath->query('//img');

        if ($images === false) {
            return 0;
        }

        $count = 0;

        foreach ($images as $image) {
            $alt = $image->attributes?->getNamedItem('alt')?->nodeValue;

            if (blank(trim((string) $alt))) {
                $count++;
            }
        }

        return $count;
    }

    private function countInternalLinks(DOMXPath $xpath, Project $project): int
    {
        $links = $xpath->query('//a[@href]');

        if ($links === false) {
            return 0;
        }

        $host = parse_url($project->url, PHP_URL_HOST);
        $count = 0;

        foreach ($links as $link) {
            $href = trim((string) $link->attributes?->getNamedItem('href')?->nodeValue);

            if ($href === '' || Str::startsWith($href, ['mailto:', 'tel:', '#'])) {
                continue;
            }

            $linkHost = parse_url($href, PHP_URL_HOST);

            if ($linkHost === null || $linkHost === $host) {
                $count++;
            }
        }

        return $count;
    }

    private function buildIssues(
        int $statusCode,
        ?string $title,
        ?string $metaDescription,
        ?string $h1,
        ?string $canonical,
        ?string $robots,
        int $imagesWithoutAlt,
        int $wordCount,
        CatalogPage $catalogPage,
    ): array {
        $issues = [];

        if ($statusCode >= 400) {
            $issues[] = ['code' => 'http_error', 'label' => 'La URL responde con error HTTP'];
        }

        if (blank($title)) {
            $issues[] = ['code' => 'missing_title', 'label' => 'Title ausente'];
        }

        if (blank($metaDescription)) {
            $issues[] = ['code' => 'missing_meta_description', 'label' => 'Meta description ausente'];
        }

        if (blank($h1)) {
            $issues[] = ['code' => 'missing_h1', 'label' => 'H1 ausente'];
        }

        if (filled($title) && Str::length($title) > 65) {
            $issues[] = ['code' => 'long_title', 'label' => 'Title demasiado largo'];
        }

        if (filled($metaDescription) && Str::length($metaDescription) > 160) {
            $issues[] = ['code' => 'long_meta_description', 'label' => 'Meta description demasiado larga'];
        }

        if ($canonical && rtrim($canonical, '/') !== rtrim($catalogPage->canonical_url ?: $catalogPage->url, '/')) {
            $issues[] = ['code' => 'canonical_mismatch', 'label' => 'Canonical no coincide con la URL esperada'];
        }

        if (Str::contains(Str::lower((string) $robots), 'noindex') && $catalogPage->is_indexable) {
            $issues[] = ['code' => 'unexpected_noindex', 'label' => 'La pagina deberia indexar pero declara noindex'];
        }

        if ($imagesWithoutAlt > 0) {
            $issues[] = ['code' => 'images_missing_alt', 'label' => 'Imagenes sin atributo alt'];
        }

        if ($wordCount < 120 && $catalogPage->type !== 'product') {
            $issues[] = ['code' => 'short_content', 'label' => 'Contenido escaso para SEO'];
        }

        return $issues;
    }

    private function metaContent(DOMXPath $xpath, string $name): ?string
    {
        $query = sprintf(
            "//meta[translate(@name, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')='%s']",
            Str::lower($name)
        );

        $node = $xpath->query($query)->item(0);

        return $node?->attributes?->getNamedItem('content')?->nodeValue;
    }

    private function firstNodeValue(DOMXPath $xpath, string $query): ?string
    {
        return Str::of((string) $xpath->query($query)->item(0)?->textContent)->squish()->value() ?: null;
    }

    private function hasIssue(array $row, string $code): bool
    {
        return collect($row['issues'] ?? [])->contains(fn (array $issue) => ($issue['code'] ?? null) === $code);
    }
}
