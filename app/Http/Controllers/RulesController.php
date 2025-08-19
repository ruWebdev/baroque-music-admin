<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class RulesController extends Controller
{
    public function index(Request $request)
    {

        $data = array();


        return Inertia::render('Rules/Rules', ['data' => $data]);
    }

    public function politics(Request $request)
    {

        $data = array();


        return Inertia::render('Rules/Politics', ['data' => $data]);
    }
}
