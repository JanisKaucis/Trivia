<?php

namespace App\Http\Controllers\Trivia;

use App\Http\Controllers\Controller;

class TriviaController extends Controller
{
    public function index()
    {
        return inertia('trivia/Trivia');
    }
}
