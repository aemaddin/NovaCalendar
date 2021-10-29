<?php

namespace Asciisd\NovaCalendar\Traits;

use Illuminate\Support\Str;
use Asciisd\NovaCalendar\Models\Event;

trait Eventable
{
    /**
     * Get all of the model's events.
     */
    public function events() {
        return $this->morphMany(Event::class, 'eventable');
    }

    public function color() {
        $conf = config("nova-calendar.eventable_types.{$this->getModelName()}");
        return $conf['colors'][$this->{$conf['color_field']}] ?? '#0070d3';
    }

    public function getModelName() {
        return Str::ucfirst($this->getTable());
    }
}