<?php

namespace Asciisd\NovaCalendar\Http\Requests;

use Illuminate\Support\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string title
 * @property int eventable_id
 * @property string eventable_type
 * @property Carbon start
 * @property Carbon end
 */
class EventRequest extends FormRequest
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
            'title'          => 'required',
            'eventable_id'   => 'required',
            'eventable_type' => 'required',
            'start'          => 'required|date',
            'end'            => 'required|date|after_or_equal:start',
        ];
    }
}
