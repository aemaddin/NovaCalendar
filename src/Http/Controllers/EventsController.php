<?php

namespace Asciisd\NovaCalendar\Http\Controllers;

use Illuminate\Http\Request;
use Asciisd\NovaCalendar\Models\Event;
use Asciisd\NovaCalendar\Http\Requests\EventRequest;
use Asciisd\NovaCalendar\Http\Requests\EventUpdateRequest;

class EventsController
{
    public function index(Request $request) {
        $events = Event::filter($request->query())
                       ->with('eventable')
                       ->get();

        return response()->json($events);
    }

    public function eventables() {
        $eventable_types = config('nova-calendar.eventable_types');

        foreach($eventable_types as $eventable_class => $eventable_meta) {
            $eventables[] = $eventable_class;
        }

        return response()->json($eventables);
    }

    public function eventableItems($eventable_type) {
        $eventable_types = config('nova-calendar.eventable_types');
        $eventable       = $eventable_types[$eventable_type]['path'];
        $display_fields  = $eventable_types[$eventable_type]['display_fields'];

        $eventables = $eventable::all('id', ...$display_fields);

        return response()->json($eventables);
    }

    public function store(EventRequest $request) {
        $new_event = $this->getModelFromRequest($request)
                          ->events()
                          ->save(Event::make([
                              'title'      => $request->title,
                              'start'      => $request->start,
                              'end'        => $request->end,
                              'recurrence' => $request->recurrence,
                          ]));

        return response()->json([
            'success' => (bool) $new_event,
            'event'   => $new_event,
        ]);
    }

    public function getModelFromRequest(EventRequest $request) {
        $eventable_type = $request->input('eventable_type');
        $eventable_id   = $request->input('eventable_id');

        $eventable = config('nova-calendar.eventable_types')[$eventable_type];

        return $eventable['path']::find($eventable_id);
    }

    public function update(EventUpdateRequest $request, $eventId) {
        $event = Event::findOrFail($eventId);

        $eventable_type = $request->input('eventable_type');

        $request->merge([
            'eventable_type' => (config('nova-calendar.eventable_types')[$eventable_type])['path'],
        ]);

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
