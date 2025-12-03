<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\News;
use App\Models\Event;

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
            'description' => ['required', 'string'],
            'performers' => ['nullable', 'string'],
            'program' => ['nullable', 'string'],
            'place' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'event_time' => ['required'],
        ]);

        $event = Event::create([
            'user_id' => self::MINI_APP_USER_ID,
            'event_type' => $validated['event_type'],
            'title' => $validated['title'],
            'short_description' => mb_substr(strip_tags($validated['description']), 0, 255),
            'long_description' => $validated['description'],
            'additional_artists' => $validated['performers'] ?? null,
            'contents' => $validated['program'] ?? null,
            'place' => $validated['place'],
            'event_date' => $validated['event_date'],
            'event_time' => $validated['event_time'],
            'page_alias' => Str::slug($validated['title'] . ' ' . $validated['event_date']),
            'age_restrictions' => '0+',
            'ticket_price_from' => null,
            'ticket_price_to' => null,
            'external_link' => null,
            'enable_page' => 0,
            'sold_out' => false,
            'featured' => false,
            'moderation_status' => 3,
            'archived' => 0,
            'main_photo' => 'events/no-event-image.jpg',
            'page_photo' => 'events/no-event-image.jpg',
            'tags' => null,
            'views_count' => 0,
        ]);

        return redirect()->back()->with('status', 'Событие отправлено. ID: ' . $event->id);
    }
}
