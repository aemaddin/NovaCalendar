<?php

namespace Asciisd\NovaCalendar\Traits;

use Asciisd\NovaCalendar\Models\Event;

trait Eventable
{
    /**
     * Get all of the model's events.
     */
    public function events()
    {
        return $this->morphMany(Event::class, 'eventable');
    }
}