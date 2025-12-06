<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Dictionary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DictionaryController extends Controller
{

    public function index(Request $request)
    {
        $data = array();

        // Список доступных первых букв (динамический алфавит)
        $letters = Dictionary::selectRaw('UPPER(LEFT(title, 1)) as letter')
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->groupBy('letter')
            ->orderBy('letter', 'ASC')
            ->pluck('letter')
            ->toArray();

        $data['letters'] = $letters;

        $letter = $request->query('letter');
        if (empty($letter) && !empty($letters)) {
            // Если буква не передана, по умолчанию берём первую доступную
            $letter = $letters[0];
        }

        if (!empty($letter)) {
            $data['currentLetter'] = $letter;
        }

        $query = Dictionary::query();
        if (!empty($letter)) {
            $query->where('title', 'LIKE', $letter . '%');
        }

        $data['dictionary'] = $query
            ->orderBy('title', 'ASC')
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
        $rawAlias = $request->page_alias ?: $request->title;
        $slug = Str::slug($rawAlias, '-');

        if ($slug !== '') {
            $dictionary->page_alias = $slug;
        }

        $dictionary->enable_page = true;

        $dictionary->save();
    }

    public function deleteDictionary(Request $request)
    {
        if ($request->id) {
            Dictionary::where('id', $request->id)->delete();
        }
    }

    /**
     * AJAX-загрузка терминов по первой букве
     */
    public function getAllDictionary(Request $request)
    {
        $letter = $request->input('letter');

        $query = Dictionary::query();
        if (!empty($letter)) {
            $query->where('title', 'LIKE', $letter . '%');
        }

        $items = $query
            ->orderBy('title', 'ASC')
            ->get();

        return response()->json($items);
    }

    public function fixAliases()
    {
        $items = Dictionary::select('id', 'title')->get();

        foreach ($items as $item) {
            $title = $item->title ?? '';
            $slug = Str::slug($title, '-');

            if ($slug === '') {
                continue;
            }

            DB::table('dictionary')
                ->where('id', $item->id)
                ->update(['page_alias' => $slug]);
        }

        return response()->json(['status' => 'ok']);
    }
}
