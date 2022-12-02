<?php

use App\Http\Controllers\HtmlController;
use App\Http\Controllers\SourceController;
use App\Services\DirService;
use Illuminate\Support\Facades\Route;
use Intervention\Image\ImageManagerStatic as Image;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::post('upload', [SourceController::class, 'index'])->middleware('auth');

Route::post('update', [HtmlController::class, 'update'])->middleware('auth');

Route::delete('delete/{dir}', [DirService::class, 'dirDelete'])->name('dirDelete')->middleware('auth');

Route::get('/delete', [DirService::class, 'delete'])->name('delete')->middleware('auth');

Route::get('/edit', [HtmlController::class, 'edit'])->name('edit')->middleware('auth');

Route::get('/newsletters', [DirService::class, 'newsletters'])->name('newsletters')->middleware('auth');

Route::get('upload/{source?}', function ($source = null) {
    return view('upload', compact("source"));
})->whereIn('upload', ['url', 'file'])->middleware('auth')->name('upload');

Route::get('/uploaded', function () {
    return view('uploaded');
})->name('uploaded');



Route::get('/get-content-url', function(){
    $url = 'https://oscarliang.com/';
    $resp = [];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    // Load HTML to DOM Object
    $dom = new DOMDocument();
    @$dom->loadHTML($data);

    // Parse DOM to get Title
    $nodes = $dom->getElementsByTagName('title');
    $title = '';
    if($nodes->length > 0){
        $title = $nodes->item(0)->nodeValue;
    }
   
    $homepage = file_get_contents('http://www.example.com/');
echo $homepage;
    return $title;
});

Route::get('/image', function() {
    //$img = Image::make('https://imgd.aeplcdn.com/0x0/n/cw/ec/27074/civic-exterior-right-front-three-quarter-148155.jpeg')->resize(300, 200);
    $img = Image::make(storage_path('app/public/images/logo_mxp.png'));
    return $img->response('jpg');
});