<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'type',
    'external_id',
    'url',
    'slug',
    'name',
    'status',
    'meta_title',
    'meta_description',
    'canonical_url',
    'is_indexable',
    'product_count',
    'payload',
])]
class CatalogPage extends Model
{
    protected function casts(): array
    {
        return [
            'is_indexable' => 'boolean',
            'payload' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
