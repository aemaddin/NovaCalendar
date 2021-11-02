<?php

namespace Asciisd\NovaCalendar\Traits;

use Illuminate\Support\Str;
use Asciisd\NovaCalendar\Models\Event;

trait Eventable
{
    /**
     * Get all the model's events.
     */
    public function events() {
        return $this->morphMany(Event::class, 'eventable');
    }

    public function color(): string {
        $conf = config("nova-calendar.eventable_types.{$this->getModelName()}");
        return $conf['colors'][$this->{$conf['color_field']}] ?? '#0070d3';
    }

    public function getModelName(): string {
        return Str::ucfirst($this->getTable());
    }
}