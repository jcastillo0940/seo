<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'project_id',
    'keyword',
    'country_code',
    'language_code',
    'device',
    'search_intent',
    'priority',
    'source',
    'last_checked_at',
])]
class TrackedKeyword extends Model
{
    protected function casts(): array
    {
        return [
            'last_checked_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function serpSnapshots(): HasMany
    {
        return $this->hasMany(SerpSnapshot::class);
    }
}
