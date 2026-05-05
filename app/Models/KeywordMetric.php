<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'keyword', 'position', 'clicks', 'impressions', 'date'])]
class KeywordMetric extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'position' => 'float',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
