<?php

use App\Http\Controllers\CustomSearchController;
use App\Http\Controllers\ParagraphController;
use App\Jobs\RunNewCustomSearchJob;
use App\Jobs\TestJob;
use App\Models\CustomSearch;
use App\Service\EmpirixService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

    return Auth::check() ? redirect('/zepplingtst') : view('auth.login');
    //return view('wombase');
})->middleware('auth')->name('base');


Route::post('descomprimir', [ParagraphController::class, 'descomprimir']);
Route::get('process', [ParagraphController::class, 'process']);
Route::post('parrafostandar/{index}', [ParagraphController::class, 'paragrafo']);
Route::get('paragraphs', [ParagraphController::class, 'paragraphs']);
Route::post('stop/decompres', [ParagraphController::class, 'stopDecompres']);

Route::group(['middleware'=>'auth'], function () {
    Route::post('custom/create', [CustomSearchController::class, 'new']);
    Route::get('custom/index', [CustomSearchController::class, 'index']);
    Route::get('custom/detail/index', [CustomSearchController::class, 'detailIndex']);
    Route::get('custom/mapurl', [CustomSearchController::class, 'getMapUrl']);
    Route::get('custom/infolast', [CustomSearchController::class, 'getLastCustomSearch']);

    Route::get('/zepplingtst', function () {

        return view('customsearch');
    });
    Route::get('/mailtest/{email}', function ($email) {

        Mail::raw('Prueba', function ($message)use($email) {
            $message->to($email)->subject("Script terminado");
        });
    });

});


Route::get('handler/end/{id}',[CustomSearchController::class,'handlerEndScript'])->name('handler.endscript');

Route::get('test',function(){
    dispatch(new TestJob());
   return \DB::table('jobs')->orderBy('id','DESC')->get()->toArray();
});

Route::get('logout', function () {
    Auth::logout();
    return redirect('/');
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
