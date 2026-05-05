<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'performance_score', 'seo_score', 'json_raw_data', 'audited_at'])]
class TechnicalAudit extends Model
{
    protected function casts(): array
    {
        return [
            'json_raw_data' => 'array',
            'audited_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
