<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'project_id',
    'tracked_keyword_id',
    'provider',
    'search_engine',
    'country_code',
    'language_code',
    'device',
    'results_count',
    'captured_at',
    'raw_payload',
])]
class SerpSnapshot extends Model
{
    protected function casts(): array
    {
        return [
            'captured_at' => 'datetime',
            'raw_payload' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function trackedKeyword(): BelongsTo
    {
        return $this->belongsTo(TrackedKeyword::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(SerpResult::class);
    }
}
