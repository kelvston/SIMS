<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('settings.all');
            Cache::forget('settings.backup');
        });
        static::deleted(function () {
            Cache::forget('settings.all');
            Cache::forget('settings.backup');
        });
    }
}
