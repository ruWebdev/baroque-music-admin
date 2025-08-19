<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Illuminate\Http\Request;

use App\Mail\ArtistModerationAccept;
use App\Mail\ArtistModerationDeny;
use Illuminate\Support\Facades\Mail;

use App\Models\Composer;

use App\Models\User;

use App\Models\Artist;
use App\Models\Band;

use App\Models\Event;
use App\Models\EventProgram;
use App\Models\EventParticipant;
use App\Models\EventPhoto;


class EventController extends Controller
{

    public function index(Request $request)
    {

        $data = array();

        $data['event_moderation'] = Event::where('moderation_status', 1)
            ->orWhere('moderation_status', 2)
            ->orderBy('event_date', 'DESC')
            ->orderBy('event_time', 'DESC')
            ->get();

        $data['events'] = Event::where('moderation_status', 0)
            ->orWhere('moderation_status', 3)
            ->orderBy('event_date', 'DESC')
            ->orderBy('event_time', 'DESC')
            ->get();

        return Inertia::render('Events/Events', ['data' => $data]);
    }


    public function createEvent(Request $request)
    {

        $newEvent = Event::create(
            [
                'user_id' => auth()->user()->id,
                'title' => $request->title,
                'event_type' => $request->event_type,
                'age_restrictons' => '0+',
                'main_photo' => 'events/no-event-image.jpg',
                'page_photo' => 'events/no-event-image.jpg',
                'enable_page' => 0,
                'archived' => 0,
                'moderation_status' => 3,
            ]
        );

        return response()->json($newEvent);
    }

    public function viewEvent($id)
    {

        $data = array();

        $data['event'] = Event::where('id', $id)
            ->with([
                'program' => function ($q) {
                    $q
                        ->leftJoin('composers', 'composers.id', '=', 'event_programs.composer_id')
                        ->select('event_programs.*', 'composers.last_name AS last_name', 'composers.first_name AS first_name')
                        ->orderBy('composers.last_name', 'ASC');
                }
            ])
            ->with([
                'participants' => function ($q) {
                    $q
                        ->leftJoin('artists', 'artists.id', '=', 'event_participants.artist_id')
                        ->leftJoin('bands', 'bands.id', '=', 'event_participants.band_id')
                        ->select('event_participants.*', 'artists.last_name AS last_name', 'artists.first_name AS first_name', 'bands.title AS title')
                        ->orderBy('artists.last_name', 'ASC')
                        ->orderBy('bands.title', 'ASC');
                }
            ])
            ->first();

        return Inertia::render('Events/ViewEvent', ['data' => $data]);
    }

    public function updateEvent($id, Request $request)
    {
        $event = Event::find($id);

        $event->title = $request->title;
        $event->age_restrictions = $request->age_restrictions;
        $event->short_description = $request->short_description;
        $event->long_description = $request->long_description;
        $event->event_date = $request->event_date;
        $event->event_time = $request->event_time;
        $event->ticket_price_from = $request->ticket_price_from;
        $event->ticket_price_to = $request->ticket_price_to;
        $event->place = $request->place;
        $event->page_alias = $request->page_alias;
        $event->external_link = $request->external_link;
        $event->enable_page = $request->enable_page;
        $event->sold_out = $request->sold_out;
        $event->featured = $request->featured;

        $event->save();
    }

    public function addEventProgram($id, Request $request)
    {
        $newEventProgram = EventProgram::create(
            [
                'composer_id' => $request->composer,
                'title' => $request->title,
                'event_id' => $id
            ]
        );

        $composer = Composer::find($request->composer);

        $newEventProgram->last_name = $composer->last_name;
        $newEventProgram->first_name = $composer->first_name;

        return response()->json($newEventProgram);
    }

    public function deleteEventProgram(Request $request)
    {
        if ($request->id) {
            EventProgram::where('id', $request->id)->delete();
        }
    }

    public function addEventParticipant($id, Request $request)
    {

        $data = array(
            'event_id' => $id,
            'type' => $request->type,
            'sorting' => 99
        );

        if ($request->type == 1) {
            $data['artist_id'] = $request->value;
            $event = Artist::find($request->value);
            $title = $event->first_name . ' ' . $event->last_name;
        } else if ($request->type == 2) {
            $data['band_id'] = $request->value;
            $band = Band::find($request->value);
            $title = $band->title;
        }

        $newEventParticipant = EventParticipant::create($data);

        if ($request->type == 1) {
            $newEventParticipant->last_name = $event->last_name;
            $newEventParticipant->first_name = $event->first_name;
        } else if ($request->type == 2) {
            $newEventParticipant->title = $title;
        }


        return response()->json($newEventParticipant);
    }

    public function deleteEventParticipant(Request $request)
    {
        if ($request->id) {
            EventParticipant::where('id', $request->id)->delete();
        }
    }

    public function acceptModeration($id)
    {
        $event = Event::find($id);
        $event->moderation_status = 3;
        $event->save();

        $user = User::find($event->user_id);

        // Mail::to($user->email)->send(new ArtistModerationAccept());
    }

    public function denyModeration($id)
    {
        $event = Event::find($id);
        $event->moderation_status = 2;
        $event->save();

        $user = User::find($event->user_id);

        // Mail::to($user->email)->send(new ArtistModerationDeny());
    }
}
