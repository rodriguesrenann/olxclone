<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;

class StateController extends Controller
{
    public function getStates()
    {
        $states = State::all();

        return response()->json([
            'data' => $states
        ]);
    }
}
