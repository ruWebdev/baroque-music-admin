<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

use App\Models\Artist;
use App\Models\ArtistPhoto;

use App\Models\Band;
use App\Models\BandPhoto;

use App\Models\Event;
use App\Models\EventPhoto;

use App\Models\News;

use App\Models\Publication;

use App\Models\Composer;
use App\Models\ComposerPhoto;

use App\Models\MusicalInstrument;
use App\Models\MusicalInstrumentPhoto;

use App\Models\Dictionary;
use App\Models\DictionaryPhoto;

class UploadController extends Controller
{

    public function uploadArtistPhoto($id, Request $request)
    {

        $manager = new ImageManager(new Driver());

        $file = $request->file('file');
        $img = $manager->read($file->path());

        $destinationPath = 'artists/' . $id . '/photo/';
        $fileName = rand() . ".jpg";

        if ($request->type == 'main_photo') {
            $img->scale(width: 300);
        } else {
            $img->scale(width: 500);
        }

        $finalImage = $img->toJpeg(90);

        $path = Storage::disk('public')->put($destinationPath . $fileName, $finalImage);

        $artist = Artist::find($id);

        if ($request->type == 'main_photo') {

            if ($artist->main_photo != '' && $artist->main_photo != 'artists/no-artist-photo.jpg') {
                Storage::disk('public')->delete($artist->main_photo);
            }

            $artist->main_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $artist->save();
        } else if ($request->type == 'page_photo') {

            if ($artist->page_photo != '' && $artist->page_photo != 'artists/no-artist-photo.jpg') {
                Storage::disk('public')->delete($artist->page_photo);
            }

            $artist->page_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $artist->save();
        } else if ($request->type == 'additional_photo') {
            $result = ArtistPhoto::create([
                'composer_id' => $id,
                'file_name' => $fileName,
                'full_path' => $destinationPath . $fileName
            ]);
        }

        return response()->json($result);
    }

    public function uploadBandPhoto($id, Request $request)
    {

        $manager = new ImageManager(new Driver());

        $file = $request->file('file');
        $img = $manager->read($file->path());

        $destinationPath = 'bands/' . $id . '/photo/';
        $fileName = rand() . ".jpg";

        if ($request->type == 'main_photo') {
            $img->scale(width: 300);
        } else {
            $img->scale(width: 800);
        }

        $finalImage = $img->toJpeg(85);

        $path = Storage::disk('public')->put($destinationPath . $fileName, $finalImage);

        $band = Band::find($id);

        if ($request->type == 'main_photo') {

            if ($band->main_photo != '' && $band->main_photo != 'bands/no-band-image.jpg') {
                Storage::disk('public')->delete($band->main_photo);
            }

            $band->main_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $band->save();
        } else if ($request->type == 'page_photo') {

            if ($band->page_photo != '' && $band->page_photo != 'bands/no-band-image.jpg') {
                Storage::disk('public')->delete($band->page_photo);
            }

            $band->page_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $band->save();
        } else if ($request->type == 'additional_photo') {
            $result = BandPhoto::create([
                'composer_id' => $id,
                'file_name' => $fileName,
                'full_path' => $destinationPath . $fileName
            ]);
        }

        return response()->json($result);
    }

    public function uploadEventPhoto($id, Request $request)
    {

        $manager = new ImageManager(new Driver());

        $file = $request->file('file');
        $img = $manager->read($file->path());

        $destinationPath = 'events/' . $id . '/photo/';
        $fileName = rand() . ".jpg";

        if ($request->type == 'main_photo') {
            $img->scale(width: 300);
        } else {
            $img->scale(width: 900);
        }

        $finalImage = $img->toJpeg(85);

        $path = Storage::disk('public')->put($destinationPath . $fileName, $finalImage);

        $event = Event::find($id);

        if ($request->type == 'main_photo') {

            if ($event->main_photo != '' && $event->main_photo != 'events/no-event-photo.jpg') {
                Storage::disk('public')->delete($event->main_photo);
            }

            $event->main_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $event->save();
        } else if ($request->type == 'page_photo') {

            if ($event->page_photo != '' && $event->page_photo != 'events/no-event-photo.jpg') {
                Storage::disk('public')->delete($event->page_photo);
            }

            $event->page_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $event->save();
        } else if ($request->type == 'additional_photo') {
            $result = ArtistPhoto::create([
                'composer_id' => $id,
                'file_name' => $fileName,
                'full_path' => $destinationPath . $fileName
            ]);
        }

        return response()->json($result);
    }

    public function uploadNewsPhoto($id, Request $request)
    {

        $manager = new ImageManager(new Driver());

        $file = $request->file('file');
        $img = $manager->read($file->path());

        $destinationPath = 'news/' . $id . '/photo/';
        $fileName = rand() . ".jpg";

        if ($request->type == 'main_photo') {
            $img->scale(width: 300);
        } else {
            $img->scale(width: 900);
        }

        $finalImage = $img->toJpeg(85);

        $path = Storage::disk('public')->put($destinationPath . $fileName, $finalImage);

        $news = News::find($id);

        if ($request->type == 'main_photo') {

            if ($news->main_photo != '' && $news->main_photo != 'news/no-event-photo.jpg') {
                Storage::disk('public')->delete($news->main_photo);
            }

            $news->main_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $news->save();
        } else if ($request->type == 'page_photo') {

            if ($news->page_photo != '' && $news->page_photo != 'news/no-event-photo.jpg') {
                Storage::disk('public')->delete($news->page_photo);
            }

            $news->page_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $news->save();
        } else if ($request->type == 'additional_photo') {
            $result = ArtistPhoto::create([
                'composer_id' => $id,
                'file_name' => $fileName,
                'full_path' => $destinationPath . $fileName
            ]);
        }

        return response()->json($result);
    }

    public function uploadPublicationPhoto($id, Request $request)
    {

        $manager = new ImageManager(new Driver());

        $file = $request->file('file');
        $img = $manager->read($file->path());

        $destinationPath = 'publications/' . $id . '/photo/';
        $fileName = rand() . ".jpg";

        if ($request->type == 'main_photo') {
            $img->scale(width: 300);
        } else {
            $img->scale(width: 900);
        }

        $finalImage = $img->toJpeg(90);

        $path = Storage::disk('public')->put($destinationPath . $fileName, $finalImage);

        $publication = Publication::find($id);

        if ($request->type == 'main_photo') {

            if ($publication->main_photo != '' && $publication->main_photo != 'publication/no-publication-image.jpg') {
                Storage::disk('public')->delete($publication->main_photo);
            }

            $publication->main_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $publication->save();
        } else if ($request->type == 'page_photo') {

            if ($publication->page_photo != '' && $publication->page_photo != 'publication/no-publication-image.jpg') {
                Storage::disk('public')->delete($publication->page_photo);
            }

            $publication->page_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $publication->save();
        } else if ($request->type == 'additional_photo') {
            $result = DictionaryPhoto::create([
                'instrument_id' => $id,
                'file_name' => $fileName,
                'full_path' => $destinationPath . $fileName
            ]);
        }

        return response()->json($result);
    }

    public function uploadInternalPublicationPhoto($id, Request $request)
    {

        $manager = new ImageManager(new Driver());

        $file = $request->file('upload');
        $img = $manager->read($file->path());

        $destinationPath = 'publications/' . $id . '/photo/';
        $fileName = rand() . ".jpg";

        if ($request->type == 'main_photo') {
            $img->scale(width: 300);
        } else {
            $img->scale(width: 900);
        }

        $finalImage = $img->toJpeg(90);

        $path = Storage::disk('public')->put($destinationPath . $fileName, $finalImage);

        $result = array();

        $result['url'] = 'http://baroquemusic.test/storage/' . $destinationPath . $fileName;

        return response()->json($result);
    }

    public function uploadComposerPhoto($id, Request $request)
    {

        $manager = new ImageManager(new Driver());



        $file = $request->file('file');
        $img = $manager->read($file->path());

        $destinationPath = 'composers/' . $id . '/photo/';
        $fileName = rand() . ".jpg";

        if ($request->type == 'main_photo') {
            $img->scale(width: 300);
        } else {
            $img->scale(width: 500);
        }

        $finalImage = $img->toJpeg(90);

        $path = Storage::disk('public')->put($destinationPath . $fileName, $finalImage);

        $composer = Composer::find($id);

        if ($request->type == 'main_photo') {

            if ($composer->main_photo != '' && $composer->main_photo != 'composers/no-composer-photo.jpg') {
                Storage::disk('public')->delete($composer->main_photo);
            }

            $composer->main_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $composer->save();
        } else if ($request->type == 'page_photo') {

            if ($composer->page_photo != '' && $composer->page_photo != 'composers/no-composer-photo.jpg') {
                Storage::disk('public')->delete($composer->page_photo);
            }

            $composer->page_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $composer->save();
        } else if ($request->type == 'additional_photo') {
            $result = ComposerPhoto::create([
                'composer_id' => $id,
                'file_name' => $fileName,
                'full_path' => $destinationPath . $fileName
            ]);
        }

        return response()->json($result);
    }

    public function uploadMusicalInstrumentPhoto($id, Request $request)
    {

        $manager = new ImageManager(new Driver());

        $file = $request->file('file');
        $img = $manager->read($file->path());

        $destinationPath = 'instruments/' . $id . '/photo/';
        $fileName = rand() . ".jpg";

        if ($request->type == 'main_photo') {
            $img->scale(width: 300);
        } else {
            $img->scale(width: 500);
        }

        $finalImage = $img->toJpeg(90);

        $path = Storage::disk('public')->put($destinationPath . $fileName, $finalImage);

        $instrument = MusicalInstrument::find($id);

        if ($request->type == 'main_photo') {

            if ($instrument->main_photo != '' && $instrument->main_photo != 'instruments/no-instrument-photo.jpg') {
                Storage::disk('public')->delete($instrument->main_photo);
            }

            $instrument->main_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $instrument->save();
        } else if ($request->type == 'page_photo') {

            if ($instrument->page_photo != '' && $instrument->page_photo != 'instruments/no-instrument-photo.jpg') {
                Storage::disk('public')->delete($instrument->page_photo);
            }

            $instrument->page_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $instrument->save();
        } else if ($request->type == 'additional_photo') {
            $result = MusicalInstrumentPhoto::create([
                'instrument_id' => $id,
                'file_name' => $fileName,
                'full_path' => $destinationPath . $fileName
            ]);
        }

        return response()->json($result);
    }

    public function uploadDictionaryPhoto($id, Request $request)
    {

        $manager = new ImageManager(new Driver());

        $file = $request->file('file');
        $img = $manager->read($file->path());

        $destinationPath = 'dictionary/' . $id . '/photo/';
        $fileName = rand() . ".jpg";

        if ($request->type == 'main_photo') {
            $img->scale(width: 300);
        } else {
            $img->scale(width: 500);
        }

        $finalImage = $img->toJpeg(90);

        $path = Storage::disk('public')->put($destinationPath . $fileName, $finalImage);

        $dictionary = Dictionary::find($id);

        if ($request->type == 'main_photo') {

            if ($dictionary->main_photo != '' && $dictionary->main_photo != 'dictionary/no-dictionary-image.jpg') {
                Storage::disk('public')->delete($dictionary->main_photo);
            }

            $dictionary->main_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $dictionary->save();
        } else if ($request->type == 'page_photo') {

            if ($dictionary->page_photo != '' && $dictionary->page_photo != 'dictionary/no-dictionary-image.jpg') {
                Storage::disk('public')->delete($dictionary->page_photo);
            }

            $dictionary->page_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;

            $dictionary->save();
        } else if ($request->type == 'additional_photo') {
            $result = DictionaryPhoto::create([
                'instrument_id' => $id,
                'file_name' => $fileName,
                'full_path' => $destinationPath . $fileName
            ]);
        }

        return response()->json($result);
    }
}
