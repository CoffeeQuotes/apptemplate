<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'options',
        'is_public',
        'display_name',
        'description',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'options' => 'array',
    ];

    // Get Setting value with optional default
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->parseValue() : $default;
        });
    }

    // Parse the value based on the setting type
    protected function parseValue()
    {
        return match($this->type) {
            'boolean' => (boolean) $this->value,
            'number' => (float) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    // Clear setting cache when updated or deleted
    protected static function booted()
    {
        static::saved(function ($setting) {
            Cache::forget("setting.{$setting->key}");
        });

        static::deleted(function ($setting) {
            Cache::forget("setting.{$setting->key}");
        });
    }
}
