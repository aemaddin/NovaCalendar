<?php

namespace Asciisd\NovaCalendar\Http\Controllers;

use Illuminate\Http\Request;
use Asciisd\NovaCalendar\Models\Event;
use Asciisd\NovaCalendar\Http\Requests\EventRequest;
use Asciisd\NovaCalendar\Http\Requests\EventUpdateRequest;

class EventsController
{
    public function index(Request $request) {
        $events = Event::filter($request->query())->get();

        return response()->json($events);
    }

    public function store(EventRequest $request) {
        $new_event = Event::create([
            'title'         => $request->title,
            'start'         => $request->start,
            'end'           => $request->end,
            'min_attendees' => $request->min_attendees,
            'max_attendees' => $request->max_attendees,
            'recurrence'    => $request->recurrence,
        ]);

        return response()->json([
            'success' => (bool) $new_event,
            'event'   => $new_event,
        ]);
    }

    public function update(EventUpdateRequest $request, $eventId) {
        $event = Event::findOrFail($eventId);

        $event->update($request->input());

        return response()->json([
            'success' => true,
            'event'   => $event,
        ]);
    }

    public function destroy(Request $request, $eventId) {
        $event = Event::findOrFail($eventId);

        if( ! is_null($event)) {
            $event->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => true]);
    }
}
