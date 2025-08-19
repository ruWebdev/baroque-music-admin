<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\User;

use App\Models\News;

class NewsController extends Controller
{

    public function index(Request $request)
    {

        $data = array();

        $data['news_moderation'] = News::where('moderation_status', 1)
            ->orWhere('moderation_status', 2)
            ->orderBy('created_at', 'DESC')
            ->get();

        $data['news'] = News::where('moderation_status', 0)
            ->orWhere('moderation_status', 3)
            ->orderBy('created_at', 'DESC')
            ->get();


        return Inertia::render('News/News', ['data' => $data]);
    }

    public function createItem(Request $request)
    {

        $newItem = News::create(
            [
                'user_id' => auth()->user()->id,
                'title' => $request->data['title'],
                'enable_page' => 0,
                'archived' => 0,
                'moderation_status' => 3,
                'status' => 0,
            ]
        );

        return response()->json($newItem);
    }

    public function viewItem($id)
    {


        $data['news'] = News::find($id);

        return Inertia::render('News/ViewNews', ['data' => $data]);
    }

    public function updateNews($id, Request $request)
    {

        $news = News::find($id);

        $news->page_alias = $request->page_alias;
        $news->title = $request->title;
        $news->short_description = $request->short_description;
        $news->long_description = $request->long_description;
        $news->external_link = $request->external_link;
        $news->enable_page = $request->enable_page;
        $news->video_type = $request->video_type;
        $news->video_code = $request->video_code;

        $news->save();
    }

    public function acceptModeration($id)
    {
        $news = News::find($id);
        $news->moderation_status = 3;
        $news->save();

        $user = User::find($news->user_id);

        // Mail::to($user->email)->send(new ArtistModerationAccept());
    }

    public function denyModeration($id)
    {
        $news = News::find($id);
        $news->moderation_status = 2;
        $news->save();

        $user = User::find($news->user_id);

        // Mail::to($user->email)->send(new ArtistModerationDeny());
    }
}
