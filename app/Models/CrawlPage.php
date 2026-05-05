<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'crawl_run_id',
    'project_id',
    'url',
    'status_code',
    'title',
    'meta_description',
    'h1',
    'canonical_url',
    'robots_directives',
    'is_indexable',
    'is_in_sitemap',
    'internal_links_count',
    'images_without_alt_count',
    'word_count',
    'issues',
])]
class CrawlPage extends Model
{
    protected function casts(): array
    {
        return [
            'is_indexable' => 'boolean',
            'is_in_sitemap' => 'boolean',
            'issues' => 'array',
        ];
    }

    public function crawlRun(): BelongsTo
    {
        return $this->belongsTo(CrawlRun::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
