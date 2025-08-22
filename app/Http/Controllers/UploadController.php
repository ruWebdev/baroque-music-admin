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

class UploadController extends Controller
{

    public function uploadArtistPhoto($id, Request $request)
    {

        $manager = new ImageManager(new Driver());

        $file = $request->file('file');
        $imgOriginal = $manager->read($file->path());

        $destinationPath = 'artists/' . $id . '/photo/';
        $artist = Artist::find($id);

        if ($request->type == 'main_photo') {
            // Delete old first
            if ($artist->main_photo && $artist->main_photo != 'artists/no-artist-photo.jpg') {
                Storage::disk('public')->delete($artist->main_photo);
            }
            // Create 100x100
            $thumb = $imgOriginal->cover(100, 100)->toWebp(quality: 90);
            $fileName = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $fileName, $thumb);
            $artist->main_photo = $destinationPath . $fileName;
            $artist->save();
            $result = $destinationPath . $fileName;
        } else if ($request->type == 'page_photo') {
            // Delete old first
            if ($artist->page_photo && $artist->page_photo != 'artists/no-artist-photo.jpg') {
                Storage::disk('public')->delete($artist->page_photo);
            }
            if ($artist->main_photo && $artist->main_photo != 'artists/no-artist-photo.jpg') {
                Storage::disk('public')->delete($artist->main_photo);
            }
            // Create page (w=500) and main (100x100)
            $pageImg = (clone $imgOriginal)->scale(width: 500)->toWebp(quality: 85);
            $thumbImg = (clone $imgOriginal)->cover(100, 100)->toWebp(quality: 90);
            $pageFile = rand() . '.webp';
            $thumbFile = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $pageFile, $pageImg);
            Storage::disk('public')->put($destinationPath . $thumbFile, $thumbImg);
            $artist->page_photo = $destinationPath . $pageFile;
            $artist->main_photo = $destinationPath . $thumbFile;
            $artist->save();
            $result = ['page_photo' => $artist->page_photo, 'main_photo' => $artist->main_photo];
        } else if ($request->type == 'additional_photo') {
            $big = $imgOriginal->scale(width: 1000)->toWebp(quality: 85);
            $fileName = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $fileName, $big);
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
        $imgOriginal = $manager->read($file->path());

        $destinationPath = 'bands/' . $id . '/photo/';
        $band = Band::find($id);

        if ($request->type == 'main_photo') {
            if ($band->main_photo && $band->main_photo != 'bands/no-band-image.jpg') {
                Storage::disk('public')->delete($band->main_photo);
            }
            $thumb = $imgOriginal->cover(100, 100)->toWebp(quality: 90);
            $fileName = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $fileName, $thumb);
            $band->main_photo = $destinationPath . $fileName;
            $band->save();
            $result = $destinationPath . $fileName;
        } else if ($request->type == 'page_photo') {
            if ($band->page_photo && $band->page_photo != 'bands/no-band-image.jpg') {
                Storage::disk('public')->delete($band->page_photo);
            }
            if ($band->main_photo && $band->main_photo != 'bands/no-band-image.jpg') {
                Storage::disk('public')->delete($band->main_photo);
            }
            $page = (clone $imgOriginal)->scale(width: 800)->toWebp(quality: 85);
            $thumb = (clone $imgOriginal)->cover(100, 100)->toWebp(90);
            $pageFile = rand() . '.webp';
            $thumbFile = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $pageFile, $page);
            Storage::disk('public')->put($destinationPath . $thumbFile, $thumb);
            $band->page_photo = $destinationPath . $pageFile;
            $band->main_photo = $destinationPath . $thumbFile;
            $band->save();
            $result = ['page_photo' => $band->page_photo, 'main_photo' => $band->main_photo];
        } else if ($request->type == 'additional_photo') {
            $big = $imgOriginal->scale(width: 1200)->toWebp(quality: 85);
            $fileName = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $fileName, $big);
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
        $imgOriginal = $manager->read($file->path());

        $destinationPath = 'events/' . $id . '/photo/';
        $event = Event::find($id);

        if ($request->type == 'main_photo') {
            if ($event->main_photo && $event->main_photo != 'events/no-event-photo.jpg') {
                Storage::disk('public')->delete($event->main_photo);
            }
            $thumb = $imgOriginal->cover(100, 100)->toWebp(quality: 90);
            $fileName = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $fileName, $thumb);
            $event->main_photo = $destinationPath . $fileName;
            $event->save();
            $result = $destinationPath . $fileName;
        } else if ($request->type == 'page_photo') {
            if ($event->page_photo && $event->page_photo != 'events/no-event-photo.jpg') {
                Storage::disk('public')->delete($event->page_photo);
            }
            if ($event->main_photo && $event->main_photo != 'events/no-event-photo.jpg') {
                Storage::disk('public')->delete($event->main_photo);
            }
            $page = (clone $imgOriginal)->scale(width: 900)->toWebp(quality: 85);
            $thumb = (clone $imgOriginal)->cover(100, 100)->toWebp(90);
            $pageFile = rand() . '.webp';
            $thumbFile = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $pageFile, $page);
            Storage::disk('public')->put($destinationPath . $thumbFile, $thumb);
            $event->page_photo = $destinationPath . $pageFile;
            $event->main_photo = $destinationPath . $thumbFile;
            $event->save();
            $result = ['page_photo' => $event->page_photo, 'main_photo' => $event->main_photo];
        } else if ($request->type == 'additional_photo') {
            $big = $imgOriginal->scale(width: 1200)->toWebp(quality: 85);
            $fileName = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $fileName, $big);
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
        $imgOriginal = $manager->read($file->path());

        $destinationPath = 'news/' . $id . '/photo/';
        $news = News::find($id);

        if ($request->type == 'main_photo') {
            if ($news->main_photo && $news->main_photo != 'news/no-event-photo.jpg') {
                Storage::disk('public')->delete($news->main_photo);
            }
            $thumb = $imgOriginal->cover(100, 100)->toWebp(quality: 90);
            $fileName = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $fileName, $thumb);
            $news->main_photo = $destinationPath . $fileName;
            $news->save();
            $result = $destinationPath . $fileName;
        } else if ($request->type == 'page_photo') {
            if ($news->page_photo && $news->page_photo != 'news/no-event-photo.jpg') {
                Storage::disk('public')->delete($news->page_photo);
            }
            if ($news->main_photo && $news->main_photo != 'news/no-event-photo.jpg') {
                Storage::disk('public')->delete($news->main_photo);
            }
            $page = (clone $imgOriginal)->scale(width: 900)->toWebp(quality: 85);
            $thumb = (clone $imgOriginal)->cover(100, 100)->toWebp(90);
            $pageFile = rand() . '.webp';
            $thumbFile = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $pageFile, $page);
            Storage::disk('public')->put($destinationPath . $thumbFile, $thumb);
            $news->page_photo = $destinationPath . $pageFile;
            $news->main_photo = $destinationPath . $thumbFile;
            $news->save();
            $result = ['page_photo' => $news->page_photo, 'main_photo' => $news->main_photo];
        } else if ($request->type == 'additional_photo') {
            $big = $imgOriginal->scale(width: 1200)->toWebp(quality: 85);
            $fileName = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $fileName, $big);
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
        $imgOriginal = $manager->read($file->path());

        $destinationPath = 'publications/' . $id . '/photo/';
        $publication = Publication::find($id);

        if ($request->type == 'main_photo') {
            if ($publication->main_photo && $publication->main_photo != 'publication/no-publication-image.jpg') {
                Storage::disk('public')->delete($publication->main_photo);
            }
            $thumb = $imgOriginal->cover(100, 100)->toWebp(quality: 90);
            $fileName = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $fileName, $thumb);
            $publication->main_photo = $destinationPath . $fileName;
            $publication->save();
            $result = $destinationPath . $fileName;
        } else if ($request->type == 'page_photo') {
            if ($publication->page_photo && $publication->page_photo != 'publication/no-publication-image.jpg') {
                Storage::disk('public')->delete($publication->page_photo);
            }
            if ($publication->main_photo && $publication->main_photo != 'publication/no-publication-image.jpg') {
                Storage::disk('public')->delete($publication->main_photo);
            }
            $page = (clone $imgOriginal)->scale(width: 900)->toWebp(quality: 85);
            $thumb = (clone $imgOriginal)->cover(100, 100)->toWebp(90);
            $pageFile = rand() . '.webp';
            $thumbFile = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $pageFile, $page);
            Storage::disk('public')->put($destinationPath . $thumbFile, $thumb);
            $publication->page_photo = $destinationPath . $pageFile;
            $publication->main_photo = $destinationPath . $thumbFile;
            $publication->save();
            $result = ['page_photo' => $publication->page_photo, 'main_photo' => $publication->main_photo];
        } else if ($request->type == 'additional_photo') {
            $big = $imgOriginal->scale(width: 1200)->toWebp(quality: 85);
            $fileName = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $fileName, $big);
            // No PublicationPhoto model exists; return basic info
            $result = [
                'file_name' => $fileName,
                'full_path' => $destinationPath . $fileName,
            ];
        }

        return response()->json($result);
    }

    public function uploadInternalPublicationPhoto($id, Request $request)
    {

        $manager = new ImageManager(new Driver());

        $file = $request->file('upload');
        $img = $manager->read($file->path());

        $destinationPath = 'publications/' . $id . '/photo/';
        $fileName = rand() . ".webp";

        if ($request->type == 'main_photo') {
            $img->scale(width: 300);
        } else {
            $img->scale(width: 900);
        }

        $finalImage = $img->toWebp(90);

        Storage::disk('public')->put($destinationPath . $fileName, $finalImage);

        $result = array();

        $result['url'] = 'http://baroquemusic.test/storage/' . $destinationPath . $fileName;

        return response()->json($result);
    }

    public function uploadComposerPhoto($id, Request $request)
    {

        $manager = new ImageManager(new Driver());



        $file = $request->file('file');
        $imgOriginal = $manager->read($file->path());

        $destinationPath = 'composers/' . $id . '/photo/';
        $fileName = rand() . ".webp";

        $composer = Composer::find($id);

        // Handle different upload types
        if ($request->type == 'main_photo') {
            // Generate square 100x100 thumbnail for main_photo
            $thumb = $imgOriginal->cover(100, 100);
            $finalImage = $thumb->toWebp(quality: 90);

            // Delete old first
            if ($composer->main_photo != '' && $composer->main_photo != 'composers/no-composer-photo.jpg') {
                Storage::disk('public')->delete($composer->main_photo);
            }

            // Then save new
            Storage::disk('public')->put($destinationPath . $fileName, $finalImage);

            $composer->main_photo = $destinationPath . $fileName;
            $result = $destinationPath . $fileName;
            $composer->save();
        } else if ($request->type == 'page_photo') {
            // Save page photo (bigger image)
            $pageImg = clone $imgOriginal;
            $pageImg->scale(width: 500);
            $pageFinal = $pageImg->toWebp(quality: 85);

            // Also create a 100x100 main_photo from original
            $thumb = clone $imgOriginal;
            $thumb = $thumb->cover(100, 100);
            $thumbFinal = $thumb->toWebp(quality: 90);

            // Filenames
            $pageFileName = rand() . '.webp';
            $thumbFileName = rand() . '.webp';

            // Delete old ones FIRST
            if ($composer->page_photo && $composer->page_photo != 'composers/no-composer-photo.jpg') {
                Storage::disk('public')->delete($composer->page_photo);
            }
            if ($composer->main_photo && $composer->main_photo != 'composers/no-composer-photo.jpg') {
                Storage::disk('public')->delete($composer->main_photo);
            }

            // Then save new files
            Storage::disk('public')->put($destinationPath . $pageFileName, $pageFinal);
            Storage::disk('public')->put($destinationPath . $thumbFileName, $thumbFinal);

            // Save new paths
            $composer->page_photo = $destinationPath . $pageFileName;
            $composer->main_photo = $destinationPath . $thumbFileName;
            $composer->save();

            $result = [
                'page_photo' => $destinationPath . $pageFileName,
                'main_photo' => $destinationPath . $thumbFileName,
            ];
        } else if ($request->type == 'additional_photo') {
            // For gallery images keep a larger size and webp
            $big = $imgOriginal->scale(width: 1000);
            $bigFinal = $big->toWebp(quality: 85);
            Storage::disk('public')->put($destinationPath . $fileName, $bigFinal);

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
        $imgOriginal = $manager->read($file->path());

        $destinationPath = 'dictionary/' . $id . '/photo/';
        $dictionary = Dictionary::find($id);

        if ($request->type == 'main_photo') {
            if ($dictionary->main_photo && $dictionary->main_photo != 'dictionary/no-dictionary-image.jpg') {
                Storage::disk('public')->delete($dictionary->main_photo);
            }
            $thumb = $imgOriginal->cover(100, 100)->toWebp(quality: 90);
            $fileName = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $fileName, $thumb);
            $dictionary->main_photo = $destinationPath . $fileName;
            $dictionary->save();
            $result = $destinationPath . $fileName;
        } else if ($request->type == 'page_photo') {
            if ($dictionary->page_photo && $dictionary->page_photo != 'dictionary/no-dictionary-image.jpg') {
                Storage::disk('public')->delete($dictionary->page_photo);
            }
            if ($dictionary->main_photo && $dictionary->main_photo != 'dictionary/no-dictionary-image.jpg') {
                Storage::disk('public')->delete($dictionary->main_photo);
            }
            $page = (clone $imgOriginal)->scale(width: 800)->toWebp(quality: 85);
            $thumb = (clone $imgOriginal)->cover(100, 100)->toWebp(90);
            $pageFile = rand() . '.webp';
            $thumbFile = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $pageFile, $page);
            Storage::disk('public')->put($destinationPath . $thumbFile, $thumb);
            $dictionary->page_photo = $destinationPath . $pageFile;
            $dictionary->main_photo = $destinationPath . $thumbFile;
            $dictionary->save();
            $result = ['page_photo' => $dictionary->page_photo, 'main_photo' => $dictionary->main_photo];
        } else if ($request->type == 'additional_photo') {
            $big = $imgOriginal->scale(width: 1000)->toWebp(quality: 85);
            $fileName = rand() . '.webp';
            Storage::disk('public')->put($destinationPath . $fileName, $big);
            // No DictionaryPhoto model; return basic info
            $result = [
                'file_name' => $fileName,
                'full_path' => $destinationPath . $fileName,
            ];
        }

        return response()->json($result);
    }
}
