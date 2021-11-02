<?php

namespace Asciisd\NovaCalendar\Exceptions;

use Exception;
use Illuminate\Support\Carbon;

class EventException extends Exception
{
    public static function limitExceed(Carbon $start): EventException {
        throw new static('Maximum limit of events reached on : ' . $start->monthName . ' ' . $start->year);
    }
}
