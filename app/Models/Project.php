<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'name',
    'url',
    'google_property_id',
    'google_property_type',
    'ga4_property_id',
    'magento_base_url',
    'magento_store_code',
    'magento_website_code',
    'last_synced_at',
    'magento_last_synced_at',
])]
class Project extends Model
{
    protected function casts(): array
    {
        return [
            'last_synced_at' => 'datetime',
            'magento_last_synced_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function keywordMetrics(): HasMany
    {
        return $this->hasMany(KeywordMetric::class);
    }

    public function technicalAudits(): HasMany
    {
        return $this->hasMany(TechnicalAudit::class);
    }

    public function competitors(): HasMany
    {
        return $this->hasMany(Competitor::class);
    }

    public function trackedKeywords(): HasMany
    {
        return $this->hasMany(TrackedKeyword::class);
    }

    public function serpSnapshots(): HasMany
    {
        return $this->hasMany(SerpSnapshot::class);
    }

    public function crawlRuns(): HasMany
    {
        return $this->hasMany(CrawlRun::class);
    }

    public function catalogPages(): HasMany
    {
        return $this->hasMany(CatalogPage::class);
    }

    public function analyticsPageMetrics(): HasMany
    {
        return $this->hasMany(AnalyticsPageMetric::class);
    }
}
