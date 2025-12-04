<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\News;
use App\Models\Event;
use App\Models\Artist;
use App\Models\Band;
use App\Models\Composer;

class TelegramMiniAppController extends Controller
{
    private const MINI_APP_USER_ID = '9bd434e2-f5dd-4f6e-88d3-25483c06e66b';

    public function showNewsForm()
    {
        return view('telegram-mini-app-news');
    }

    public function storeNews(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news', 'public');
        }

        $news = News::create([
            'user_id' => self::MINI_APP_USER_ID,
            'title' => $validated['title'],
            'short_description' => mb_substr(strip_tags($validated['body']), 0, 255),
            'long_description' => $validated['body'],
            'page_alias' => Str::slug($validated['title']),
            'main_photo' => $imagePath,
            'page_photo' => $imagePath,
            'moderation_status' => 3,
            'enable_page' => 0,
            'external_link' => null,
            'video_type' => null,
            'video_code' => null,
            'page_views' => 0,
        ]);

        return redirect()->back()->with('status', 'Новость отправлена. ID: ' . $news->id);
    }

    public function showEventForm()
    {
        return view('telegram-mini-app-event');
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'event_type' => ['required', 'integer', 'in:1,2,3'],
            'title' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'place' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'event_time' => ['required'],
            'short_description' => ['nullable', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'ticket_price_from' => ['required', 'string', 'max:255'],
            'ticket_price_to' => ['nullable', 'string', 'max:255'],
            'age_restrictions' => ['required', 'string', 'in:0+,6+,12+,16+,18+'],
            'performers' => ['nullable', 'string'],
            'program' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $shortDescription = $validated['short_description'] ?? null;
        if ($shortDescription === null || trim($shortDescription) === '') {
            $shortDescription = mb_substr(strip_tags($validated['description']), 0, 150);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
        }

        $fullPlace = trim($validated['city'] . ' — ' . $validated['place']);

        $event = Event::create([
            'user_id' => self::MINI_APP_USER_ID,
            'event_type' => $validated['event_type'],
            'title' => $validated['title'],
            // Краткое и полное описание страницы события
            'short_description' => $shortDescription,
            'long_description' => $validated['description'],
            // Исполнители и программа сохраняются в текстовом виде
            'contents' => $validated['program'] ?? null,
            'place' => $fullPlace,
            'event_date' => $validated['event_date'],
            'event_time' => $validated['event_time'],
            'page_alias' => Str::slug($validated['title'] . ' ' . $validated['event_date']),
            'age_restrictions' => $validated['age_restrictions'],
            'ticket_price_from' => $validated['ticket_price_from'],
            'ticket_price_to' => $validated['ticket_price_to'] ?? null,
            'external_link' => null,
            'enable_page' => 0,
            'sold_out' => false,
            'featured' => false,
            'moderation_status' => 3,
            'archived' => 0,
            'main_photo' => $imagePath ?: 'events/no-event-image.jpg',
            'page_photo' => $imagePath ?: 'events/no-event-image.jpg',
            'tags' => null,
            'views_count' => 0,
        ]);

        return redirect()->back()->with('status', 'Событие отправлено. ID: ' . $event->id);
    }

    public function searchArtists(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $artists = Artist::query()
            ->where(function ($sub) use ($q) {
                $sub->where('last_name', 'LIKE', "%$q%")
                    ->orWhere('first_name', 'LIKE', "%$q%");
            })
            ->orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->limit(50)
            ->get(['id', 'last_name', 'first_name']);

        return response()->json($artists);
    }

    public function searchBands(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $bands = Band::query()
            ->where('title', 'LIKE', "%$q%")
            ->orderBy('title', 'ASC')
            ->limit(50)
            ->get(['id', 'title']);

        return response()->json($bands);
    }

    public function searchComposers(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $composers = Composer::query()
            ->where(function ($sub) use ($q) {
                $sub->where('last_name', 'LIKE', "%$q%")
                    ->orWhere('first_name', 'LIKE', "%$q%")
                    ->orWhere('last_name_en', 'LIKE', "%$q%")
                    ->orWhere('first_name_en', 'LIKE', "%$q%");
            })
            ->orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->limit(50)
            ->get(['id', 'last_name', 'first_name']);

        return response()->json($composers);
    }
}
