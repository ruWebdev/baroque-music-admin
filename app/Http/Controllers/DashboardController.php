<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{

    public function index(Request $request)
    {

        $data = array();

        return Inertia::render('Dashboard/Dashboard', ['data' => $data]);
    }
}
