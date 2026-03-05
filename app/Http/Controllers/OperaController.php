<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Opera;
use App\Models\Composer;
use Illuminate\Support\Facades\Storage;

class OperaController extends Controller
{
    public function index(Request $request)
    {
        $data = [];

        $letter = $request->query('letter');

        $query = Opera::with('composer');
        if (!empty($letter)) {
            $data['currentLetter'] = $letter;
            $query->where('title', 'LIKE', $letter . '%');
            $data['operas'] = $query
                ->orderBy('title', 'ASC')
                ->get();
        } else {
            $size = (int) $request->query('size', 50);
            $paginator = $query
                ->orderBy('title', 'ASC')
                ->simplePaginate($size);

            $data['operas'] = $paginator->items();
            $data['pagination'] = [
                'next_page' => $paginator->hasMorePages() ? $paginator->currentPage() + 1 : null,
                'per_page' => $paginator->perPage(),
            ];
        }

        return Inertia::render('Operas/Operas', ['data' => $data]);
    }

    public function getAllOperas(Request $request)
    {
        $letter = $request->input('letter');

        $query = Opera::with('composer');
        if (!empty($letter)) {
            $query->where('title', 'LIKE', $letter . '%');
        }

        if (!empty($letter)) {
            $operas = $query
                ->orderBy('title', 'ASC')
                ->get();

            return response()->json($operas);
        }

        $page = (int) $request->input('page', 1);
        $size = (int) $request->input('size', 50);

        $paginator = $query
            ->orderBy('title', 'ASC')
            ->simplePaginate($size, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginator->items(),
            'next_page' => $paginator->hasMorePages() ? $paginator->currentPage() + 1 : null,
            'per_page' => $paginator->perPage(),
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $operas = Opera::with('composer')
            ->where(function ($sub) use ($q) {
                $sub->where('title', 'LIKE', "%$q%")
                    ->orWhere('title_en', 'LIKE', "%$q%");
            })
            ->orderBy('title', 'ASC')
            ->limit(200)
            ->get();

        return response()->json($operas);
    }

    public function createOpera(Request $request)
    {
        $newItem = Opera::create(
            [
                'title' => $request->data['title'],
                'main_photo' => 'operas/no-opera-image.jpg',
                'page_photo' => 'operas/no-opera-image.jpg',
                'enable_page' => true,
            ]
        );

        return response()->json($newItem);
    }

    public function viewOpera($id)
    {
        $data = [];

        $data['opera'] = Opera::with('composer')->find($id);
        $data['composers'] = Composer::orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->get(['id', 'last_name', 'first_name', 'last_name_en', 'first_name_en']);

        return Inertia::render('Operas/ViewOpera', ['data' => $data]);
    }

    public function updateOpera($id, Request $request)
    {
        $opera = Opera::find($id);

        $opera->title = $request->title;
        $opera->title_en = $request->title_en;
        $opera->year_created = $request->year_created;
        $opera->composer_id = $request->composer_id;
        $opera->short_description = $request->short_description;
        $opera->long_description = $request->long_description;
        $opera->vk_video_link = $request->vk_video_link;
        $opera->imslp_link = $request->imslp_link;
        $opera->page_alias = $request->page_alias;
        $opera->enable_page = (bool) $request->enable_page;

        $opera->save();
    }

    public function deleteOpera($id)
    {
        $opera = Opera::find($id);
        if (!$opera) {
            return response()->json(['message' => 'Opera not found'], 404);
        }

        $placeholders = [
            'operas/no-opera-image.jpg',
        ];
        if (!empty($opera->main_photo) && !in_array($opera->main_photo, $placeholders, true)) {
            Storage::disk('public')->delete($opera->main_photo);
        }
        if (!empty($opera->page_photo) && !in_array($opera->page_photo, $placeholders, true)) {
            Storage::disk('public')->delete($opera->page_photo);
        }

        $opera->delete();

        return response()->json(['message' => 'Opera deleted']);
    }
}
