<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasSlug;

class AttributeValue extends Model
{
    use HasSlug;

    protected $fillable = [
        'attribute_id',
        'value',
        'slug'
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
} 