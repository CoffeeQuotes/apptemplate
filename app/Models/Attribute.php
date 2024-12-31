<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    protected $fillable = [
        'name',
        'type',
        'is_filterable',
        'is_required'
    ];

    protected $casts = [
        'is_filterable' => 'boolean',
        'is_required' => 'boolean',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
} 