<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Quote;

class QuoteController extends Controller
{

    public function index(Request $request)
    {

        $data = array();

        $data['quotes'] = Quote::orderBy('created_at', 'ASC')
            ->get();


        return Inertia::render('Quotes/Quotes', ['data' => $data]);
    }

    public function createQuote(Request $request)
    {

        $newItem = Quote::create(
            [
                'author' => $request->author,
                'title' => $request->title,
            ]
        );

        return response()->json($newItem);
    }

    public function viewQuote($id)
    {

        $data = array();

        $data['quote'] = Quote::find($id);

        return Inertia::render('Quotes/ViewQuote', ['data' => $data]);
    }

    public function updateQuote($id, Request $request)
    {
        $quote = Quote::find($id);

        $quote->author = $request->author;
        $quote->title = $request->title;
        $quote->long_description = $request->long_description;

        $quote->save();
    }

    public function deleteQuote(Request $request)
    {
        if ($request->id) {
            Quote::where('id', $request->id)->delete();
        }
    }
}
