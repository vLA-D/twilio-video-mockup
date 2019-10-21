<?php

namespace App\Http\Controllers;

use App\Events\CallReceived;
use Illuminate\Http\Request;

/**
 * Class GenericController
 * @package App\Http\Controllers
 */
class GenericController extends Controller
{
    public function callAgent(Request $request)
    {
        CallReceived::dispatch($request->get('token'), $request->get('room'));
    }
}
