<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null): mixed
    {
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Returns true if the store is currently open:
     *  - `is_open` flag must be '1'
     *  - current time must be between open_time and close_time
     */
    public static function isCurrentlyOpen(): bool
    {
        if (static::get('is_open', '1') !== '1') {
            return false;
        }

        $openTime  = static::get('open_time',  '08:00');
        $closeTime = static::get('close_time', '22:00');

        $now   = now()->format('H:i');
        return $now >= $openTime && $now <= $closeTime;
    }
}
