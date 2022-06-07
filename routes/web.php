<?php

use App\Http\Controllers\ParagraphController;
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
});


Route::post('descomprimir',[ParagraphController::class,'descomprimir']);
Route::get('process',[ParagraphController::class,'process']);
Route::post('parrafostandar/{index}',[ParagraphController::class,'paragrafo']);
Route::get('paragraphs',[ParagraphController::class,'paragraphs']);
Route::get('/zepplingtst', function () {
    /*$obj = new ZeppelinAPI\Zeppelin(['baseUrl' => env('ZEPLLING_HOST')]);
    $result = $obj->paragraph()->runParagraphSync('2H5MVKKF1','20220522-173128_2133226449',[
            "params"=>[
                "date"=>"lograste!!"
            ]
    ]);
    //dd($result);
    //dd($result);
    dd(explode("\n",$result->body->msg[0]->data));*/
    return view('welcome');
});

Route::get('/test', function () {
    $obj = new ZeppelinAPI\Zeppelin(['baseUrl' => env('ZEPLLING_HOST')]);
    $result = $obj->paragraph()->runParagraphSync('2H3HB7613','paragraph_1654041145659_1836033409',[
            "params"=>[
                "fecha"=>"lograste!!"
            ]
    ]);
    dd($result);
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
