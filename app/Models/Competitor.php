<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['project_id', 'domain', 'name', 'notes', 'last_seen_at'])]
class Competitor extends Model
{
    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function serpResults(): HasMany
    {
        return $this->hasMany(SerpResult::class);
    }
}
