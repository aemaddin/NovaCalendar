<?php

namespace Asciisd\NovaCalendar\Observers;

use Illuminate\Support\Carbon;
use Asciisd\NovaCalendar\Models\Event;

class RecurrenceObserver
{
    public static function created(Event $event) {
        if( ! $event->event()->exists()) {
            $recurrences = [
                'daily'   => [
                    'times'    => 365,
                    'function' => 'addDay',
                ],
                'weekly'  => [
                    'times'    => 52,
                    'function' => 'addWeek',
                ],
                'monthly' => [
                    'times'    => 12,
                    'function' => 'addMonth',
                ],
            ];
            $start       = Carbon::parse($event->start);
            $end         = Carbon::parse($event->end);
            $recurrence  = $recurrences[$event->recurrence] ?? null;

            if($recurrence) {
                for($i = 0; $i < $recurrence['times']; $i++) {
                    $start->{$recurrence['function']}();
                    $end->{$recurrence['function']}();
                    $event->events()->create([
                        'title'         => $event->title,
                        'start'         => $start,
                        'end'           => $end,
                        'min_attendees' => $event->min_attendees,
                        'max_attendees' => $event->max_attendees,
                        'recurrence'    => $event->recurrence,
                    ]);
                }
            }
        }
    }

    public function updated(Event $event) {
        if($event->events()->exists() || $event->event) {
            $start   = $event->getOriginal('start')
                             ->diffInSeconds($event->start, false);
            $endTime =
                $event->getOriginal('end')->diffInSeconds($event->end, false);
            if($event->event) {
                $childEvents = $event->event->events()
                                            ->whereDate('start', '>',
                                                $event->getOriginal('start'))
                                            ->get();
            } else {
                $childEvents = $event->events;
            }

            foreach($childEvents as $childEvent) {
                if($start) {
                    $childEvent->start =
                        $childEvent->start->addSeconds($start);
                }
                if($endTime) {
                    $childEvent->end = $childEvent->end->addSeconds($endTime);
                }
                if($event->isDirty('title')
                   && $childEvent->title == $event->getOriginal('title')) {
                    $childEvent->title = $event->title;
                }

                $childEvent->saveQuietly();
            }
        }

        if($event->isDirty('recurrence') && $event->recurrence != 'none') {
            self::created($event);
        }
    }

    public function deleted(Event $event) {
        if($event->events()->exists()) {
            $events = $event->events()->pluck('id');
        } elseif($event->event) {
            $events = $event->event->events()->whereDate('start', '>',
                $event->start)->pluck('id');
        } else {
            $events = [];
        }

        Event::whereIn('id', $events)->delete();
    }
}