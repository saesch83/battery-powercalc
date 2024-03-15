<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HerstellerController;
use App\Http\Controllers\LeistungController;
use App\Models\Schrank;
use App\Models\Typ;
use App\Http\Controllers\TypController;
use App\Models\Anlage;
use App\Models\Anlagenparameter;
use App\Models\Anlagenleistung;
use App\Http\Controllers\PowerCalcController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



/*
|
| Hersteller
|
*/
Route::middleware('client')->get('/hersteller', [HerstellerController::Class, 'index']);

Route::middleware('client')->get('/hersteller/{id}', [HerstellerController::Class, 'show']);

Route::middleware('client')->get('/hersteller/{id}/typen', [HerstellerController::Class, 'typen']);


/*
|
| Leistungen
|
*/

Route::middleware('client')->get('/leistungen', [LeistungController::Class, 'index']);

Route::middleware('client')->get('/leistung/{id}', [LeistungController::Class, 'show']);


/*
|
| Typen
|
*/

Route::middleware('client')->get('/typen', [TypController::Class, 'index']);

Route::middleware('client')->get('/typ/{id}', [TypController::Class, 'show']);

Route::middleware('client')->get('/typ/{id}/hersteller', [TypController::Class, 'hersteller']);



/*
|
| USV-Anlage
|
*/
Route::middleware('client')->get('/usv-anlagen', function(){

    return Anlage::all();
});

/*
|
| USV-Parameter
|
*/
Route::middleware('client')->get('/usv-parameter', function(){

    return Anlagenparameter::all();
});

/*
|
| USV-Leistungen
|
*/
Route::middleware('client')->get('/usv-leistungen', function(){

    return Anlagenleistung::all();
});



/*
|
| Berechnungen
|
*/
Route::middleware('client')->post('/berechnung_batterie', [PowerCalcController::Class, 'batteryCalc']);