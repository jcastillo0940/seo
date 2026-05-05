<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'serp_snapshot_id',
    'competitor_id',
    'domain',
    'url',
    'title',
    'position',
    'is_own_domain',
    'estimated_ctr',
    'estimated_traffic',
])]
class SerpResult extends Model
{
    protected function casts(): array
    {
        return [
            'is_own_domain' => 'boolean',
            'estimated_ctr' => 'float',
        ];
    }

    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(SerpSnapshot::class, 'serp_snapshot_id');
    }

    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }
}
