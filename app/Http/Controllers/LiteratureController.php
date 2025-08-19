<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Literature;

class LiteratureController extends Controller
{

    public function index(Request $request)
    {

        $data = array();

        $data['instruments'] = MusicalInstrument::orderBy('title', 'ASC')
            ->get();


        return Inertia::render('Instruments/Instruments', ['data' => $data]);
    }

    public function createMusicalInstrument(Request $request)
    {

        $newItem = MusicalInstrument::create(
            [
                'title' => $request->data['title'],
                'main_photo' => 'instruments/no-instrument-photo.jpg',
                'page_photo' => 'instruments/no-instrument-photo.jpg'
            ]
        );

        return response()->json($newItem);
    }

    public function viewMusicalInstrument($id)
    {

        $data = array();

        $data['instrument'] = MusicalInstrument::find($id);

        return Inertia::render('Instruments/ViewInstrument', ['data' => $data]);
    }

    public function updateMusicalInstrument($id, Request $request)
    {
        $composer = MusicalInstrument::find($id);

        $composer->title = $request->title;
        $composer->title_rod = $request->title_rod;
        $composer->short_description = $request->short_description;
        $composer->long_description = $request->long_description;
        $composer->page_alias = $request->page_alias;
        $composer->enabled = true;

        $composer->save();
    }

    public function getAllInstruments(Request $request)
    {


        $instruments = MusicalInstrument::orderBy('title', 'ASC')
            ->get();

        return response()->json($instruments);
    }

    public function createInstrumentFromSelect(Request $request)
    {

        $instrument = MusicalInstrument::create([
            'title' => $request->title
        ]);

        return response()->json($instrument);
    }
}
