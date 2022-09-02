<?php

use App\Http\Controllers\CustomSearchController;
use App\Http\Controllers\ParagraphController;
use App\Jobs\RunNewCustomSearchJob;
use App\Models\CustomSearch;
use App\Service\EmpirixService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    return view('wombase');
})->middleware('auth')->name('base');


Route::post('descomprimir', [ParagraphController::class, 'descomprimir']);
Route::get('process', [ParagraphController::class, 'process']);
Route::post('parrafostandar/{index}', [ParagraphController::class, 'paragrafo']);
Route::get('paragraphs', [ParagraphController::class, 'paragraphs']);
Route::post('stop/decompres', [ParagraphController::class, 'stopDecompres']);
Route::post('custom/create', [CustomSearchController::class, 'new']);
Route::get('custom/index', [CustomSearchController::class, 'index']);
Route::get('custom/detail/index', [CustomSearchController::class, 'detailIndex']);
Route::get('custom/mapurl', [CustomSearchController::class, 'getMapUrl']);

Route::get('/zepplingtst', function () {

    return view('customsearch');
});


Route::get('test2', function () {
    $service = new EmpirixService(env('EMPIRIX_USER'), env('EMPIRIX_PASSWORD'));
    dd($service->getData());

    return "trabajo agregado";
});

Route::get('test', function () {
    $last = CustomSearch::orderBy('id', 'ASC')->first();

    dd(CustomSearch::orderBy('id', 'ASC')->first()->toArray());
    $customSearch =  CustomSearch::create(['day' => '2022-01-01', 'hour' => '11']);
    $job = new RunNewCustomSearchJob($customSearch);
    dispatch($job);
    return "trabajo agregado";
});
Route::get('logout', function () {
    Auth::logout();
    return redirect('/');
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
