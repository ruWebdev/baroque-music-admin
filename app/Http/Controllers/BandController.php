<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Illuminate\Http\Request;

use App\Mail\BandModerationAccept;
use App\Mail\BandModerationDeny;
use Illuminate\Support\Facades\Mail;

use App\Models\User;

use App\Models\Artist;

use App\Models\Band;
use App\Models\BandParticipant;
use App\Models\BandPhoto;


class BandController extends Controller
{

    public function index(Request $request)
    {

        $data = array();

        $data['band_moderation'] = Band::where('moderation_status', 1)
            ->orWhere('moderation_status', 2)
            ->orderBy('updated_at', 'DESC')
            ->get();

        $data['bands'] = Band::where('moderation_status', 0)
            ->orWhere('moderation_status', 3)
            ->orderBy('title', 'ASC')
            ->get();

        return Inertia::render('Bands/Bands', ['data' => $data]);
    }

    public function getAllBands(Request $request)
    {

        $band = Band::orderBy('title', 'ASC')
            ->get();

        return response()->json($band);
    }

    public function createItem(Request $request)
    {

        $newItem = Band::create(
            [
                'user_id' => auth()->user()->id,
                'title' => $request->data['title'],
                'enable_page' => 0,
                'moderation_status' => 3
            ]
        );

        return response()->json($newItem);
    }


    public function createBand(Request $request)
    {

        if ($request->band_id == null && $request->participation == 1) {
            $band = Band::create(
                [
                    'user_id' => auth()->user()->id,
                    'title' => $request->title,
                    'enable_page' => false,
                    'moderation_status' => 3,
                    'status' => 0,
                ]
            );
            BandParticipant::create([
                'user_id' => auth()->user()->id,
                'band_id' => $band->id,
                'role' => 'creator',
            ]);
        } else if ($request->band_id == null && $request->participation == 2) {
            $band = Band::create(
                [
                    'title' => $request->title,
                    'enable_page' => false,
                    'moderation_status' => 3,
                    'status' => 0,
                ]
            );
            BandParticipant::create([
                'user_id' => auth()->user()->id,
                'band_id' => $band->id,
                'role' => 'participant',
            ]);
        }

        $bands = BandParticipant::where('user_id', auth()->user()->id)
            ->with('band')
            ->get();

        return response()->json($bands);
    }

    public function createBandFromSelect(Request $request)
    {

        $band = Band::create([
            'title' => $request->title,
            'main_photo' => 'bands/no-band-image.jpg',
            'page_photo' => 'bands/no-band-image.jpg',
            'enable_page' => 0,
            'moderation_status' => 3,
        ]);

        return response()->json($band);
    }

    public function connectBand(Request $request)
    {

        BandParticipant::create([
            'user_id' => auth()->user()->id,
            'band_id' => $request->band_id,
            'role' => 'participant',
        ]);

        $bands = BandParticipant::where('user_id', auth()->user()->id)
            ->with('band')
            ->get();

        return response()->json($bands);
    }

    public function disconnectBand(Request $request)
    {

        BandParticipant::where('user_id', auth()->user()->id)
            ->where('band_id', $request->band_id)
            ->delete();

        $bands = BandParticipant::where('user_id', auth()->user()->id)
            ->with('band')
            ->get();

        return response()->json($bands);
    }

    public function viewBand($id)
    {

        $data = array();

        $data['band'] = Band::where('bands.id', $id)
            ->with([
                'participants' => function ($q) {
                    $q
                        ->leftJoin('artists', 'artists.id', '=', 'band_participants.artist_id')
                        ->select('band_participants.*', 'artists.last_name AS last_name', 'artists.first_name AS first_name')
                        ->orderBy('artists.last_name', 'ASC');
                }
            ])->first();
        $data['band_photos'] = BandPhoto::where('band_id', $id)->get();

        return Inertia::render('Bands/ViewBand', ['data' => $data]);
    }

    public function updateBand($id, Request $request)
    {
        $band = Band::find($id);

        $band->title = $request->title;
        $band->short_description = $request->short_description;
        $band->long_description = $request->long_description;
        $band->country = $request->country;
        $band->city = $request->city;
        $band->page_alias = $request->page_alias;
        $band->external_link = $request->external_link;
        $band->enable_page = $request->enable_page;
        $band->video_type = $request->video_type;
        $band->video_code = $request->video_code;

        $band->save();
    }

    public function searchBand(Request $request)
    {

        $band = 'not found';


        $result = Band::where('title', 'LIKE', '%' . $request->title . '%')
            ->first();

        if ($result) {
            $band = $result;
        }

        return response()->json($band);
    }

    public function addBandParticipant($id, Request $request)
    {

        $data = array(
            'band_id' => $id,
        );

        $artist = Artist::find($request->value);

        $data['artist_id'] = $artist->id;
        $data['artist_id'] = $artist->id;

        if ($artist->user_id != null) {
            $data['user_id'] = $artist->user_id;
            $data['role'] = 'participant';
        }

        $newBandParticipant = BandParticipant::create($data);

        $newBandParticipant->last_name = $artist->last_name;
        $newBandParticipant->first_name = $artist->first_name;


        return response()->json($newBandParticipant);
    }

    public function deleteBandParticipant(Request $request)
    {
        if ($request->id) {
            BandParticipant::where('id', $request->id)->delete();
        }
    }

    public function acceptModeration($id)
    {
        $band = Band::find($id);
        $band->moderation_status = 3;
        $band->save();

        $user = User::find($band->user_id);

        if ($user) {
            Mail::to($user->email)->send(new BandModerationAccept());
        }
    }

    public function denyModeration($id)
    {
        $band = Band::find($id);
        $band->moderation_status = 2;
        $band->save();

        $user = User::find($band->user_id);

        if ($user) {
            Mail::to($user->email)->send(new BandModerationDeny());
        }
    }
}
