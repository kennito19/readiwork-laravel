<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = ['key', 'value', 'label', 'type', 'group'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember('setting_' . $key, 300, function () use ($key, $default) {
            $s = static::where('key', $key)->first();
            return $s ? $s->value : $default;
        });
    }

    public static function servicePrice(string $serviceSlug): int
    {
        $defaults = [
            'identity-verification'   => 99,
            'credit-score-check'      => 199,
            'crb-blacklist-check'     => 149,
            'loan-eligibility'        => 349,
            'full-credit-report'      => 499,
        ];
        $key = 'price_' . str_replace('-', '_', $serviceSlug);
        $val = static::get($key);
        if ($val !== null && is_numeric($val)) return (int) $val;
        return $defaults[$serviceSlug] ?? 99;
    }

    public static function allSettings(): array
    {
        return Cache::remember('all_settings', 300, function () {
            return static::orderBy('group')->orderBy('id')->get()
                ->keyBy('key')
                ->map(fn($s) => [
                    'value' => $s->value,
                    'label' => $s->label,
                    'type'  => $s->type ?? 'text',
                    'group' => $s->group ?? 'general',
                ])->toArray();
        });
    }

    public static function grouped(): array
    {
        $all = static::allSettings();
        $grouped = [];
        foreach ($all as $key => $setting) {
            $grouped[$setting['group']][$key] = $setting;
        }
        return $grouped;
    }
}
