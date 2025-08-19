<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Mail\ArtistModerationAccept;
use App\Mail\ArtistModerationDeny;
use Illuminate\Support\Facades\Mail;

use App\Models\User;
use App\Models\Artist;
use App\Models\MusicalInstrument;

class ArtistController extends Controller
{

    public function index(Request $request)
    {

        $data = array();

        $data['artist_moderation'] = Artist::where('moderation_status', 1)
            ->orWhere('moderation_status', 2)
            ->orderBy('updated_at', 'DESC')
            ->get();

        $data['artists'] = Artist::where('moderation_status', 0)
            ->orWhere('moderation_status', 3)
            ->orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->get();

        return Inertia::render('Artists/Artists', ['data' => $data]);
    }

    public function getAllArtists(Request $request)
    {

        $artists = Artist::orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->where('last_name', '!=', null)
            ->where('first_name', '!=', null)
            ->get();

        return response()->json($artists);
    }

    public function createArtist(Request $request)
    {

        $artist = Artist::create([
            'last_name' => $request->data['last_name'],
            'first_name' => $request->data['first_name'],
            'middle_name' => $request->data['middle_name'],
            'user_id' => auth()->user()->id,
            'main_photo' => 'artists/no-artist-image.jpg',
            'enable_page' => false,
            'moderation_status' => 3,
        ]);

        return response()->json($artist);
    }

    public function createArtistFromSelect(Request $request)
    {

        $name = explode(',', $request->full_name);

        $artist = Artist::create([
            'last_name' => trim($name[0]),
            'first_name' => trim($name[1]),
            'main_photo' => 'artists/no-artist-image.jpg',
            'page_photo' => 'artists/no-artist-image.jpg'
        ]);

        return response()->json($artist);
    }

    public function requestModeration($id)
    {
        $artist = Artist::find($id);
        $artist->moderation_status = 1;
        $artist->save();
    }

    public function connectArtist(Request $request)
    {

        $artist = Artist::where('last_name', $request->last_name)
            ->where('first_name', $request->first_name)
            ->first();

        $artist->user_id = auth()->user()->id;
        $artist->save();

        $user = User::find(auth()->user()->id);
        $user->is_connected_to_artist = true;
        $user->save();

        return response('success');
    }

    public function viewArtist($id)
    {

        $data = array();

        $data['artist'] = Artist::find($id);

        return Inertia::render('Artists/ViewArtist', ['data' => $data]);
    }


    public function updateArtist($id, Request $request)
    {

        $artist = Artist::find($id);

        $artist->page_alias = $request->page_alias;
        $artist->last_name = $request->last_name;
        $artist->first_name = $request->first_name;
        $artist->middle_name = $request->middle_name;
        $artist->birth_place = $request->birth_place;
        $artist->birth_date = $request->birth_date;
        $artist->musical_instruments = $request->musical_instruments;
        $artist->short_description = $request->short_description;
        $artist->long_description = $request->long_description;
        $artist->show_birth_place = $request->show_birth_place;
        $artist->show_birth_date = $request->show_birth_date;
        $artist->enable_page = $request->enable_page;
        $artist->video_type = $request->video_type;
        $artist->video_code = $request->video_code;

        $artist->save();
    }

    public function searchArtist(Request $request)
    {

        $artists = Artist::where('last_name', $request->last_name)
            ->where('first_name', $request->first_name)
            ->where('user_id', null)
            ->get();

        return response()->json($artists);
    }

    public function acceptModeration($id)
    {
        $artist = Artist::find($id);
        $artist->moderation_status = 3;
        $artist->save();

        $user = User::find($artist->user_id);

        if ($user) {
            Mail::to($user->email)->send(new ArtistModerationAccept());
        }
    }

    public function denyModeration($id)
    {
        $artist = Artist::find($id);
        $artist->moderation_status = 2;
        $artist->save();

        $user = User::find($artist->user_id);

        if ($user) {
            Mail::to($user->email)->send(new ArtistModerationDeny());
        }
    }
}
