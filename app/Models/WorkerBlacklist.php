<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WorkerBlacklist extends Model
{
    protected $table = 'worker_blacklists';

    protected $fillable = [
        'nik',
        'full_name',
        'is_blacklisted',
        'blacklist_type',
        'blacklisted_until',
        'reason',
        'blacklisted_by',
    ];

    protected function casts(): array
    {
        return [
            'is_blacklisted' => 'boolean',
            'blacklisted_until' => 'date',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_blacklisted', true)
            ->where(function (Builder $q) {
                $q->where('blacklist_type', 'permanent')
                    ->orWhere(function (Builder $temp) {
                        $temp->where('blacklist_type', 'temporary')
                            ->whereNotNull('blacklisted_until')
                            ->whereDate('blacklisted_until', '>=', Carbon::today());
                    });
            });
    }

    public static function isNikBlacklisted(string $nik): bool
    {
        return static::query()
            ->active()
            ->where('nik', $nik)
            ->exists();
    }
}
