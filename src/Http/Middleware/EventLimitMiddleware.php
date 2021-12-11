<?php

namespace Asciisd\NovaCalendar\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Asciisd\NovaCalendar\Models\Event;
use Asciisd\NovaCalendar\Exceptions\EventException;

class EventLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $start_datetime = $request->start;
        if(Event::limitReachedWithin($start_datetime)) {
            EventException::limitExceed($start_datetime);
        }

        return $next($request);
    }
}
