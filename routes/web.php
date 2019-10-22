<?php

use App\Events\TestEvent;

Route::get('/agent', function () {
    return view('agent');
});
Route::get('/client', function () {
    return view('client');
});

//Route::get('fire-test-event', function () {
//    TestEvent::dispatch();
//});
//
//Route::get('call-agent', 'GenericController@callAgent');
