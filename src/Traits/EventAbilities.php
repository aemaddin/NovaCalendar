<?php

namespace Asciisd\NovaCalendar\Traits;

use Illuminate\Support\Str;
use Asciisd\NovaCalendar\Models\Event;

trait EventAbilities
{
    public static function limitReachedWithin($start_date): bool {
        $config_limit = config('nova-calendar.max_limit_of_one_day', 0);

        if($config_limit) {
            return Event::whereDate('start', $start_date)->count()
                   >= $config_limit;
        }

        return false;
    }

    /**
     * Get the parent eventable model (user or post).
     */
    public function eventable() {
        return $this->morphTo();
    }

    public function getColorAttribute($value) {
        if( ! $value) {
            return $this->eventable->color();
        }
    }

    public function scopeFilter($query, $data) {
        if( ! empty($data['start'])) {
            $query->where('start', '>=', $data['start']);
        }

        if( ! empty($data['end'])) {
            $query->where('end', '<=', $data['end']);
        }

        return $query;
    }

    public function getEventableNameAttribute() {
        return Str::ucfirst($this->eventable()->first()->getTable());
    }

    public function limitReached(): bool {
        return self::limitReachedWithin($this->start);
    }
}