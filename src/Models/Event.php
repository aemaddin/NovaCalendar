<?php

namespace Asciisd\NovaCalendar\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string title
 * @property string color
 * @property Carbon start
 * @property Carbon end
 */
class Event extends Model
{
    protected $appends = ['eventable_name'];

    protected $casts = [
        'start' => 'datetime',
        'end'   => 'datetime',
    ];

    protected $guarded = ['id'];

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
}