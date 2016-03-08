<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class WebController extends Controller
{
    public function getIndex()
    {
        return view('app.index');
    }
}
