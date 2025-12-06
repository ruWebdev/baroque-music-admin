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
use App\Models\EventParticipant;
use App\Models\EventProgram;

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
        ], [
            'required' => 'Поле :attribute обязательно для заполнения.',
            'string' => 'Поле :attribute должно быть текстом.',
            'image' => 'Поле :attribute должно быть изображением (JPG, PNG и т.п.).',
            'max.string' => 'Поле :attribute не должно превышать :max символов.',
            'max.file' => 'Файл :attribute не должен превышать :max килобайт.',
            'title.required' => 'Пожалуйста, введите заголовок.',
            'body.required' => 'Пожалуйста, введите текст новости.',
            'image.max' => 'Изображение не должно превышать 5 МБ.',
        ], [
            'title' => 'Заголовок',
            'body' => 'Текст новости',
            'image' => 'Изображение',
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

        return redirect()->back()->with('status', 'Спасибо, новость отправлена на модерацию');
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
        ], [
            'required' => 'Поле :attribute обязательно для заполнения.',
            'string' => 'Поле :attribute должно быть текстом.',
            'integer' => 'Поле :attribute должно быть числом.',
            'date' => 'Поле :attribute должно быть корректной датой.',
            'image' => 'Поле :attribute должно быть изображением (JPG, PNG и т.п.).',
            'max.string' => 'Поле :attribute не должно превышать :max символов.',
            'max.file' => 'Файл :attribute не должен превышать :max килобайт.',
            'in' => 'Выбрано недопустимое значение для поля :attribute.',
            'event_type.required' => 'Пожалуйста, выберите тип события.',
            'event_type.in' => 'Выбран неверный тип события.',
            'title.required' => 'Пожалуйста, введите название события.',
            'city.required' => 'Пожалуйста, укажите город.',
            'place.required' => 'Пожалуйста, укажите место проведения.',
            'event_date.required' => 'Пожалуйста, укажите дату события.',
            'event_time.required' => 'Пожалуйста, укажите время события.',
            'description.required' => 'Пожалуйста, введите подробное описание события.',
            'ticket_price_from.required' => 'Пожалуйста, укажите стоимость билетов «от».',
            'age_restrictions.required' => 'Пожалуйста, выберите возрастное ограничение.',
            'age_restrictions.in' => 'Выбрано неверное возрастное ограничение.',
            'image.max' => 'Изображение не должно превышать 5 МБ.',
        ], [
            'event_type' => 'Тип события',
            'title' => 'Название события',
            'city' => 'Город',
            'place' => 'Место проведения',
            'event_date' => 'Дата события',
            'event_time' => 'Время события',
            'short_description' => 'Краткое описание',
            'description' => 'Подробное описание',
            'ticket_price_from' => 'Стоимость билетов от',
            'ticket_price_to' => 'Стоимость билетов до',
            'age_restrictions' => 'Возрастное ограничение',
            'performers' => 'Исполнители',
            'program' => 'Программа',
            'image' => 'Изображение',
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
            'views_count' => 0,
        ]);

        $this->attachParticipantsFromMiniApp($event, $request->input('participants_payload'), $validated['performers'] ?? null);
        $this->attachProgramFromMiniApp($event, $validated['program'] ?? null);

        return redirect()->back()->with('status', 'Спасибо, событие отправлено на модерацию');
    }

    /**
     * Создание участников события на основе данных из Telegram mini app.
     * Если передан JSON participants_payload — используем его.
     * Иначе разбираем текстовое поле performers построчно как вручную введённых артистов.
     */
    private function attachParticipantsFromMiniApp(Event $event, ?string $participantsPayload, ?string $performersText): void
    {
        $items = [];

        if (!empty($participantsPayload)) {
            $decoded = json_decode($participantsPayload, true);
            if (is_array($decoded)) {
                $items = $decoded;
            }
        }

        // Дополнительно разбираем текстовое поле исполнителей и добавляем
        // вручную введённых артистов, которых нет в структурированном списке.
        if (!empty($performersText)) {
            $existingLabels = [];
            foreach ($items as $p) {
                if (is_array($p) && !empty($p['label'])) {
                    $existingLabels[] = (string) $p['label'];
                }
            }

            $lines = preg_split("/(\r\n|\n|\r)/", $performersText) ?: [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                if (in_array($line, $existingLabels, true)) {
                    continue;
                }

                $items[] = [
                    'type' => 'artist_manual',
                    'full_name' => $line,
                ];
            }
        }

        foreach ($items as $item) {
            if (!is_array($item) || empty($item['type'])) {
                continue;
            }

            if ($item['type'] === 'artist') {
                $artistId = $item['id'] ?? null;
                if (!$artistId) {
                    continue;
                }
                $artist = Artist::find($artistId);
                if (!$artist) {
                    continue;
                }

                EventParticipant::create([
                    'event_id' => $event->id,
                    'artist_id' => $artist->id,
                    'type' => 1,
                    'sorting' => 99,
                ]);
            } elseif ($item['type'] === 'band') {
                $bandId = $item['id'] ?? null;
                if (!$bandId) {
                    continue;
                }
                $band = Band::find($bandId);
                if (!$band) {
                    continue;
                }

                EventParticipant::create([
                    'event_id' => $event->id,
                    'band_id' => $band->id,
                    'type' => 2,
                    'sorting' => 99,
                ]);
            } elseif ($item['type'] === 'artist_manual') {
                $fullName = trim((string) ($item['full_name'] ?? ''));
                if ($fullName === '') {
                    continue;
                }

                // Пытаемся найти артиста по любым комбинациям имени/фамилии
                $artistQuery = Artist::query();
                $this->applyNameTokensFilter($artistQuery, $fullName, ['last_name', 'first_name']);

                $artist = $artistQuery
                    ->orderBy('last_name', 'ASC')
                    ->orderBy('first_name', 'ASC')
                    ->first();

                // Если не нашли — создаём нового артиста
                if (!$artist) {
                    $lastName = $fullName;
                    $firstName = null;

                    if (strpos($fullName, ',') !== false) {
                        [$last, $first] = array_map('trim', explode(',', $fullName, 2));
                        $lastName = $last;
                        $firstName = $first;
                    } else {
                        $parts = preg_split('/\s+/u', $fullName, -1, PREG_SPLIT_NO_EMPTY);
                        if (count($parts) > 1) {
                            $lastName = array_pop($parts);
                            $firstName = implode(' ', $parts);
                        }
                    }

                    $artist = Artist::create([
                        'last_name' => $lastName,
                        'first_name' => $firstName,
                        'main_photo' => 'artists/no-artist-image.jpg',
                        'page_photo' => 'artists/no-artist-image.jpg',
                        'enable_page' => false,
                        'moderation_status' => 3,
                    ]);
                }

                EventParticipant::create([
                    'event_id' => $event->id,
                    'artist_id' => $artist->id,
                    'type' => 1,
                    'sorting' => 99,
                ]);
            }
        }
    }

    /**
     * Сохранить программу события из текстового поля в event_programs.
     */
    private function attachProgramFromMiniApp(Event $event, ?string $programText): void
    {
        $programText = (string) $programText;
        if (trim($programText) === '') {
            return;
        }

        $lines = preg_split("/(\r\n|\n|\r)/", $programText) ?: [];
        $sorting = 1;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $composerName = null;
            $title = null;

            if (preg_match('/^(.*?)[\s–—-]+(.*)$/u', $line, $m)) {
                $composerName = trim($m[1]);
                $title = trim($m[2]);
            } else {
                // Если дефис не найден — считаем, что указано только имя композитора
                $composerName = $line;
                $title = null;
            }

            if ($composerName === '') {
                continue;
            }

            $composer = $this->findComposerByName($composerName);
            if (!$composer) {
                // Новых композиторов не создаём — просто пропускаем строку
                continue;
            }

            EventProgram::create([
                'event_id' => $event->id,
                'composer_id' => $composer->id,
                'title' => $title,
                'sorting' => $sorting++,
            ]);
        }
    }

    /**
     * Поиск артиста/композитора по произвольной строке имени с учётом всех слов.
     * Каждый токен должен присутствовать хотя бы в одном из указанных столбцов.
     */
    private function applyNameTokensFilter($query, string $name, array $columns): void
    {
        $tokens = preg_split('/[\s,]+/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        foreach ($tokens as $token) {
            $token = trim($token);
            if ($token === '') {
                continue;
            }
            $query->where(function ($sub) use ($token, $columns) {
                foreach ($columns as $idx => $column) {
                    if ($idx === 0) {
                        $sub->where($column, 'LIKE', "%$token%");
                    } else {
                        $sub->orWhere($column, 'LIKE', "%$token%");
                    }
                }
            });
        }
    }

    /**
     * Найти композитора по строке имени, учитывая любые комбинации first/last name.
     */
    private function findComposerByName(string $name): ?Composer
    {
        $query = Composer::query();
        $this->applyNameTokensFilter($query, $name, ['last_name', 'first_name', 'last_name_en', 'first_name_en']);

        return $query
            ->orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->first();
    }

    public function searchArtists(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $query = Artist::query();
        $this->applyNameTokensFilter($query, $q, ['last_name', 'first_name']);

        $artists = $query
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

        $query = Composer::query();
        $this->applyNameTokensFilter($query, $q, ['last_name', 'first_name', 'last_name_en', 'first_name_en']);

        $composers = $query
            ->orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->limit(50)
            ->get(['id', 'last_name', 'first_name']);

        return response()->json($composers);
    }
}
