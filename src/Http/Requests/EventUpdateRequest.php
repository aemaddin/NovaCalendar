<?php

namespace Asciisd\NovaCalendar\Http\Requests;

use Illuminate\Support\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string title
 * @property int min_attendees
 * @property int max_attendees
 * @property Carbon start
 * @property Carbon end
 */
class EventUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'title'         => 'required',
            'start'         => 'required|date',
            'min_attendees' => 'sometimes|numeric|min:1',
            'max_attendees' => 'sometimes|numeric|min:1',
            'end'           => 'required|date|after_or_equal:start',
        ];
    }
}
