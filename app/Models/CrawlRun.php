<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'project_id',
    'source',
    'status',
    'pages_crawled',
    'issue_count',
    'started_at',
    'finished_at',
    'summary',
])]
class CrawlRun extends Model
{
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'summary' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(CrawlPage::class);
    }
}
