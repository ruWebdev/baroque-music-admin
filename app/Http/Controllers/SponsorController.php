<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SponsorController extends Controller
{

    public function index(Request $request)
    {

        $data = array();


        return Inertia::render('Sponsor/Sponsor', ['data' => $data]);
    }
}
