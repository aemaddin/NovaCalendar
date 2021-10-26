<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

// Route::get('/endpoint', function (Request $request) {
//     //
// });

Route::get('/events', 'EventsController@index');
Route::get('/eventables', 'EventsController@eventables');
Route::get('/eventables/{eventable_type}', 'EventsController@eventableItems');
Route::post('/events/store', 'EventsController@store');
Route::put('/events/{event_id}/update', 'EventsController@update');
Route::delete('/events/{event_id}/destroy', 'EventsController@destroy');
