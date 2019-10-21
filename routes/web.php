<?php

use App\Events\TestEvent;

Route::get('/', function () {
    return view('welcome');
});

Route::get('fire-test-event', function () {
    TestEvent::dispatch();
});

Route::get('call-agent', 'GenericController@callAgent');
