<?php

namespace Asciisd\NovaCalendar\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Asciisd\NovaCalendar\Traits\EventAbilities;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property int min_attendees
 * @property int max_attendees
 * @property string title
 * @property string color
 * @property Carbon start
 * @property Carbon end
 * @property string recurrence
 * @property Collection events
 */
class Event extends Model
{
    use SoftDeletes;
    use EventAbilities;

    const RECURRENCE_RADIO = [
        'none'    => 'None',
        'daily'   => 'Daily',
        'weekly'  => 'Weekly',
        'monthly' => 'Monthly',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end'   => 'datetime',
    ];

    protected $guarded = ['id'];

    public function events() {
        return $this->hasMany(Event::class, 'event_id', 'id');
    }

    public function event() {
        return $this->belongsTo(Event::class, 'event_id');
    }
}