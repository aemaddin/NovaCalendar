<?php

namespace Asciisd\NovaCalendar\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Asciisd\NovaCalendar\Traits\EventAbilities;

/**
 * @property int id
 * @property string title
 * @property string color
 * @property Carbon start
 * @property Carbon end
 */
class Event extends Model
{
    use EventAbilities;

    protected $appends = ['eventable_name'];

    protected $casts = [
        'start' => 'datetime',
        'end'   => 'datetime',
    ];

    protected $guarded = ['id'];
}