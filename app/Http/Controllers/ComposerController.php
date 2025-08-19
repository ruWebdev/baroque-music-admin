<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Composer;

class ComposerController extends Controller
{

    public function index(Request $request)
    {

        $data = array();

        $letter = $request->query('letter');

        $query = Composer::query();
        if (!empty($letter)) {
            $data['currentLetter'] = $letter;
            $query->where('last_name', 'LIKE', $letter . '%');
            // Для выбранной буквы — грузим всех
            $data['composers'] = $query
                ->orderBy('last_name', 'ASC')
                ->orderBy('first_name', 'ASC')
                ->get();
        } else {
            // Для вкладки "Все" — пагинация, чтобы не грузить всех сразу
            $size = (int) $request->query('size', 50);
            $paginator = $query
                ->orderBy('last_name', 'ASC')
                ->orderBy('first_name', 'ASC')
                ->simplePaginate($size);

            $data['composers'] = $paginator->items();
            $data['pagination'] = [
                'next_page' => $paginator->hasMorePages() ? $paginator->currentPage() + 1 : null,
                'per_page' => $paginator->perPage(),
            ];
        }

        return Inertia::render('Composers/Composers', ['data' => $data]);
    }

    public function getAllComposers(Request $request)
    {

        $letter = $request->input('letter');

        $query = Composer::query();
        if (!empty($letter)) {
            $query->where('last_name', 'LIKE', $letter . '%');
        }

        // Если выбрана буква — возвращаем полный список без пагинации
        if (!empty($letter)) {
            $composers = $query
                ->orderBy('last_name', 'ASC')
                ->orderBy('first_name', 'ASC')
                ->get();

            return response()->json($composers);
        }

        // Иначе — пагинация для вкладки "Все"
        $page = (int) $request->input('page', 1);
        $size = (int) $request->input('size', 50);

        $paginator = $query
            ->orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->simplePaginate($size, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginator->items(),
            'next_page' => $paginator->hasMorePages() ? $paginator->currentPage() + 1 : null,
            'per_page' => $paginator->perPage(),
        ]);
    }

    public function createComposer(Request $request)
    {

        $newItem = Composer::create(
            [
                'last_name' => $request->data['last_name'],
                'first_name' => $request->data['first_name'],
                'main_photo' => 'composers/no-composer-image.jpg',
                'page_photo' => 'composers/no-composer-image.jpg'
            ]
        );

        return response()->json($newItem);
    }

    public function createComposerFromSelect(Request $request)
    {

        $name = explode(',', $request->full_name);

        $instrument = Composer::create([
            'last_name' => trim($name[0]),
            'first_name' => trim($name[1]),
            'main_photo' => 'composers/no-composer-image.jpg',
            'page_photo' => 'composers/no-composer-image.jpg'
        ]);

        return response()->json($instrument);
    }

    public function viewComposer($id)
    {

        $data = array();

        $data['composer'] = Composer::find($id);

        return Inertia::render('Composers/ViewComposer', ['data' => $data]);
    }

    public function updateComposer($id, Request $request)
    {
        $composer = Composer::find($id);

        $composer->last_name = $request->last_name;
        $composer->last_name_en = $request->last_name_en;
        $composer->last_name_rod = $request->last_name_rod;
        $composer->first_name = $request->first_name;
        $composer->first_name_en = $request->first_name_en;
        $composer->first_name_rod = $request->first_name_rod;
        $composer->first_name_short = $request->first_name_short;
        $composer->first_name_short_en = $request->first_name_short_en;
        $composer->birth_date = $request->birth_date;
        $composer->death_date = $request->death_date;
        $composer->short_description = $request->short_description;
        $composer->long_description = $request->long_description;
        $composer->imslp_link = $request->imslp_link;
        $composer->video_type = $request->video_type;
        $composer->video_code = $request->video_code;
        $composer->page_alias = $request->page_alias;
        $composer->enabled = true;

        $composer->save();
    }
}
