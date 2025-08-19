<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\BandController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\SponsorController;

use App\Http\Controllers\ComposerController;
use App\Http\Controllers\MusicalInstrumentController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\LiteratureController;
use App\Http\Controllers\QuoteController;

use App\Http\Controllers\UploadController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Контроллер при входе в систему
Route::controller(DashboardController::class)->middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/dashboard', 'index')->name('dashboard'); // Отправная точка        
    }
);

Route::controller(ArtistController::class)->middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/artists', 'index')->name('artists'); // Отправная точка   

        Route::post('/artists/create', 'createArtist')->name('artists.create'); // Отправная точка       

        Route::get('/artists/view/{id}', 'viewArtist')->name('artists.view'); // Отправная точка       
        Route::post('/artists/save_changes/{id}', 'updateArtist')->name('artist.update'); // Отправная точка  

        Route::post('/artist/accept_moderation/{id}', 'acceptModeration')->name('artist.accept_moderation'); // Отправная точка 
        Route::post('/artist/deny_moderation/{id}', 'denyModeration')->name('artist.deny_moderation'); // Отправная точка 

        Route::post('/artists/get_all', 'getAllArtists')->name('artists.get_all'); // Отправная точка        
        Route::post('/artists/create_from_select', 'createArtistFromSelect')->name('artists.create_from_select'); // Отправная точка    

    }
);

Route::controller(BandController::class)->middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/bands', 'index')->name('bands'); // Отправная точка       

        Route::post('/bands/get_all', 'getAllBands')->name('bands.get_all'); // Отправная точка        
        Route::post('/bands/create', 'createItem')->name('bands.create'); // Отправная точка     
        Route::post('/bands/create_from_select', 'createBandFromSelect')->name('bands.create_from_select'); // Отправная точка    

        Route::get('/bands/view/{id}', 'viewBand')->name('bands.view'); // Отправная точка       
        Route::post('/bands/save_changes/{id}', 'updateBand')->name('bands.update'); // Отправная точка  

        Route::post('/bands/add_participant/{id}', 'addBandParticipant')->name('bands.add_participant'); // Отправная точка        
        Route::post('/bands/delete_participant/{id}', 'deleteBandParticipant')->name('bands.delete_participant'); // Отправная точка  

        Route::post('/bands/accept_moderation/{id}', 'acceptModeration')->name('bands.accept_moderation'); // Отправная точка 
        Route::post('/bands/deny_moderation/{id}', 'denyModeration')->name('bands.deny_moderation'); // Отправная точка 

    }
);

Route::controller(EventController::class)->middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/events', 'index')->name('events'); // Отправная точка  

        Route::get('/events/view/{id}', 'viewEvent')->name('events.view'); // Отправная точка        
        Route::post('/events/create', 'createEvent')->name('events.create'); // Отправная точка        
        Route::post('/events/save_changes/{id}', 'updateEvent')->name('events.update'); // Отправная точка    

        Route::post('/events/add_program/{id}', 'addEventProgram')->name('events.add_program'); // Отправная точка        
        Route::post('/events/delete_program/{id}', 'deleteEventProgram')->name('events.delete_program'); // Отправная точка        

        Route::post('/events/add_participant/{id}', 'addEventParticipant')->name('events.add_participant'); // Отправная точка        
        Route::post('/events/delete_participant/{id}', 'deleteEventParticipant')->name('events.delete_participant'); // Отправная точка  

        Route::post('/events/accept_moderation/{id}', 'acceptModeration')->name('events.accept_moderation'); // Отправная точка 
        Route::post('/events/deny_moderation/{id}', 'denyModeration')->name('events.deny_moderation'); // Отправная точка 

    }
);

Route::controller(NewsController::class)->middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/news', 'index')->name('news'); // Отправная точка      

        Route::get('/news/view/{id}', 'viewItem')->name('news.view'); // Отправная точка        
        Route::post('/news/create', 'createItem')->name('news.create'); // Отправная точка     

        Route::post('/news/save_changes/{id}', 'updateNews')->name('news.update'); // Отправная точка  

        Route::post('/news/accept_moderation/{id}', 'acceptModeration')->name('news.accept_moderation'); // Отправная точка 
        Route::post('/news/deny_moderation/{id}', 'denyModeration')->name('news.deny_moderation'); // Отправная точка 
    }
);

Route::controller(PublicationController::class)->middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/publications', 'index')->name('publications'); // Отправная точка    

        Route::get('/publications/view/{id}', 'viewPublication')->name('publications.view'); // Отправная точка        
        Route::post('/publications/create', 'createPublication')->name('publications.create'); // Отправная точка        
        Route::post('/publications/save_changes/{id}', 'updatePublication')->name('publications.update'); // Отправная точка  

        Route::post('/publications/accept_moderation/{id}', 'acceptModeration')->name('publications.accept_moderation'); // Отправная точка 
        Route::post('/publications/deny_moderation/{id}', 'denyModeration')->name('publications.deny_moderation'); // Отправная точка 

    }
);

Route::controller(RulesController::class)->middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/rules', 'index')->name('rules'); // Отправная точка        
        Route::get('/politics', 'politics')->name('politics'); // Отправная точка        
    }
);

Route::controller(SponsorController::class)->middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/sponsor', 'index')->name('sponsor'); // Отправная точка        
    }
);

Route::controller(ComposerController::class)->middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/composers', 'index')->name('composers'); // Отправная точка      

        Route::get('/composers/view/{id}', 'viewComposer')->name('composers.view'); // Отправная точка        
        Route::post('/composers/create', 'createComposer')->name('composer.create'); // Отправная точка        
        Route::post('/composers/save_changes/{id}', 'updateComposer')->name('composers.update'); // Отправная точка    

        Route::post('/composers/get_all', 'getAllComposers')->name('composers.get_all'); // Отправная точка        
        Route::post('/composers/create_from_select', 'createComposerFromSelect')->name('composers.create_from_select'); // Отправная точка 
    }
);

Route::controller(MusicalInstrumentController::class)->middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/instruments', 'index')->name('instruments'); // Отправная точка      

        Route::get('/instruments/view/{id}', 'viewMusicalInstrument')->name('instruments.view'); // Отправная точка        
        Route::post('/instruments/create', 'createMusicalInstrument')->name('instruments.create'); // Отправная точка        
        Route::post('/instruments/save_changes/{id}', 'updateMusicalInstrument')->name('instruments.update'); // Отправная точка  

        Route::post('/musical_instruments/get_all', 'getAllInstruments')->name('musical_instruments.get_all'); // Отправная точка        
        Route::post('/musical_instruments/create_from_select', 'createInstrumentFromSelect')->name('musical_instruments.get_all');
    }
);

Route::controller(DictionaryController::class)->middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/dictionary', 'index')->name('dictionary'); // Отправная точка    

        Route::get('/dictionary/view/{id}', 'viewDictionary')->name('dictionary.view'); // Отправная точка        
        Route::post('/dictionary/create', 'createDictionary')->name('dictionary.create'); // Отправная точка        
        Route::post('/dictionary/save_changes/{id}', 'updateDictionary')->name('dictionary.update'); // Отправная точка    

        Route::post('/dictionary/delete', 'deleteDictionary')->name('dictionary.delete'); // Отправная точка    

    }
);

Route::controller(LiteratureController::class)->middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/literature', 'index')->name('publications'); // Отправная точка    

        Route::get('/literature/view/{id}', 'viewLiterature')->name('literature.view'); // Отправная точка        
        Route::post('/literature/create', 'createLiterature')->name('literature.create'); // Отправная точка        
        Route::post('/literature/save_changes/{id}', 'updateLiterature')->name('literature.update'); // Отправная точка    

    }
);

Route::controller(QuoteController::class)->middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/quotes', 'index')->name('quotes'); // Отправная точка      

        Route::get('/quotes/view/{id}', 'viewQuote')->name('quotes.view'); // Отправная точка        
        Route::post('/quotes/create', 'createQuote')->name('quotes.create'); // Отправная точка        
        Route::post('/quotes/save_changes/{id}', 'updateQuote')->name('quotes.update'); // Отправная точка   

        Route::post('/quotes/delete', 'deleteQuote')->name('quotes.delete'); // Отправная точка    
    }
);

Route::controller(UploadController::class)->middleware(['auth', 'verified'])->group(
    function () {
        Route::post('/upload/composer_photo/{id}', 'uploadComposerPhoto')->name('uploads.composer_photo'); // Отправная точка        
        Route::post('/upload/instrument_photo/{id}', 'uploadMusicalInstrumentPhoto')->name('uploads.instrument_photo'); // Отправная точка  

        Route::post('/upload/artist_photo/{id}', 'uploadArtistPhoto')->name('uploads.artist_photo');
        Route::post('/upload/artist_photo/{id}/delete', 'deleteArtistPhoto')->name('uploads.delete_artist_photo'); // Отправная точка        

        Route::post('/upload/band_photo/{id}', 'uploadBandPhoto')->name('uploads.band_photo'); // Отправная точка        
        Route::post('/upload/band_photo/{id}/delete', 'deleteBandPhoto')->name('uploads.delete_band_photo'); // Отправная точка  

        Route::post('/upload/event_photo/{id}', 'uploadEventPhoto')->name('uploads.event_photo');
        Route::post('/upload/event_photo/{id}/delete', 'deleteEventPhoto')->name('uploads.delete_event_photo'); // Отправная точка        

        Route::post('/upload/news_photo/{id}', 'uploadNewsPhoto')->name('uploads.news_photo');
        Route::post('/upload/news_photo/{id}/delete', 'deleteNewsPhoto')->name('uploads.delete_news_photo'); // Отправная точка        

        Route::post('/upload/publication_photo/{id}', 'uploadPublicationPhoto')->name('uploads.publication_photo');
        Route::post('/upload/publication_photo/upload_image/{id}', 'uploadInternalPublicationPhoto')->name('uploads.upload_publication_photo');
        Route::post('/upload/publication_photo/{id}/delete', 'deletePublicationPhoto')->name('uploads.delete_publication_photo');

        Route::post('/upload/dictionary_photo/{id}', 'uploadDictionaryPhoto')->name('uploads.dictionary_photo');
        Route::post('/upload/dictionary_photo/{id}/delete', 'deleteDictionaryPhoto')->name('uploads.delete_dictionary_photo');

        Route::post('/upload/litearure_photo/{id}', 'uploadLiteraturePhoto')->name('uploads.literature_photo');
        Route::post('/upload/literature_photo/{id}/delete', 'deleteLiteraturePhoto')->name('uploads.delete_literature_photo');
    }
);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/mailable', function () {


    return new App\Mail\ArtistModerationAccept();
});

require __DIR__ . '/auth.php';
