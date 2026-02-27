<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class LiveController extends Controller
{
    public function index()
    {
        return view('officer.live');
    }
}
