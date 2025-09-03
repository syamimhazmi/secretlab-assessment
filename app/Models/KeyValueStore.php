<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeyValueStore extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'value' => 'json',
        'stored_at' => 'datetime',
    ];

    public static function getLatestValue(string $key)
    {
        return static::query()
            ->where('key', $key)
            ->orderBy('stored_at', 'desc')
            ->first();
    }

    public static function getValueAtTimestamp(string $key, int $timestamp)
    {
        if ($timestamp <= 0) {
            throw new \InvalidArgumentException('Invalid timestamp');
        }

        $datetime = Carbon::createFromTimestamp($timestamp);

        return static::query()
            ->where('key', $key)
            ->where('stored_at', '<=', $datetime)
            ->orderBy('stored_at', 'desc')
            ->first();
    }

    public static function storeKeyValue(string $key, mixed $value, ?int $timestamp): KeyValueStore
    {
        $timestamp = $timestamp ?: time();
        $datetime = Carbon::createFromTimestamp($timestamp);

        return static::query()
            ->create([
                'key' => $key,
                'value' => $value,
                'stored_at' => $datetime,
            ]);
    }

    public static function getAllLatestValues(): array
    {
        return static::query()
            ->select('key', 'value', 'stored_at')
            ->orderBy('key')
            ->get()
            ->map(function ($record) {
                return [
                    'key' => $record->getAttribute('key'),
                    'value' => $record->getAttribute('value'),
                    'stored_at' => $record->getAttribute('stored_at')->toDateTimeString(),
                ];
            })->toArray();
    }
}
