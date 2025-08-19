<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Dictionary;

class DictionaryController extends Controller
{

    public function index(Request $request)
    {

        $data = array();

        $data['dictionary'] = Dictionary::orderBy('title', 'ASC')
            ->get();


        return Inertia::render('Dictionary/Dictionary', ['data' => $data]);
    }

    public function createDictionary(Request $request)
    {

        $newItem = Dictionary::create(
            [
                'title' => $request->title,
                'main_photo' => 'dictionary/no-dictionary-image.jpg',
                'page_photo' => 'dictionary/no-instrument-image.jpg'
            ]
        );

        return response()->json($newItem);
    }

    public function viewDictionary($id)
    {

        $data = array();

        $data['dictionary'] = Dictionary::find($id);

        return Inertia::render('Dictionary/ViewDictionary', ['data' => $data]);
    }

    public function updateDictionary($id, Request $request)
    {
        $dictionary = Dictionary::find($id);

        $dictionary->title = $request->title;
        $dictionary->origin_language = $request->origin_language;
        $dictionary->transcription = $request->transcription;
        $dictionary->short_description = $request->short_description;
        $dictionary->long_description = $request->long_description;
        $dictionary->external_link = $request->external_link;
        $dictionary->page_alias = $request->page_alias;
        $dictionary->enable_page = true;

        $dictionary->save();
    }

    public function deleteDictionary(Request $request)
    {
        if ($request->id) {
            Dictionary::where('id', $request->id)->delete();
        }
    }
}
