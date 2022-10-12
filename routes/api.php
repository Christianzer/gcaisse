<?php

use App\Http\Controllers\CaisseControllers;
use App\Http\Controllers\PersonnesControllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get("code",[CaisseControllers::class,'generercodeentre']);
Route::get('personne',[PersonnesControllers::class,'index']);
Route::post('personne', [PersonnesControllers::class,'createClient']);
Route::post('upload', [CaisseControllers::class,'upload']);
Route::put('personne/{id}', [PersonnesControllers::class,'updateClient']);
Route::delete('personne/{id}', [PersonnesControllers::class,'deleteClient']);
Route::get('dashboard',[CaisseControllers::class,'index']);

Route::get('listes_entre',[CaisseControllers::class,'entre_listes']);
Route::get('listes_justif/{code}',[CaisseControllers::class,'listes_justif']);
Route::post('facture',[CaisseControllers::class,'entrer_caisse']);
Route::post('paiement',[CaisseControllers::class,'paiement']);

Route::post('sortie',[CaisseControllers::class,'sortir_caisse']);
Route::get('factures_caisses/{id}',[CaisseControllers::class,'listes_factures_entree']);
Route::get('versements/{id}',[CaisseControllers::class,'listes_versements']);
Route::get('sorties/{id}',[CaisseControllers::class,'listes_sorties']);

Route::get('dowload/{id}',[CaisseControllers::class,'dowload']);


Route::get('listes_sortie',[CaisseControllers::class,'sortie_listes']);
Route::get('listes_justif_sortie/{code}',[CaisseControllers::class,'listes_justif_sortie']);
Route::get('dowload_sortie/{id}',[CaisseControllers::class,'dowload_sortie']);
Route::get("code_sortie",[CaisseControllers::class,'generercodeentre_sortie']);
Route::post('facture_sortie',[CaisseControllers::class,'sortirCaisse']);
Route::post('upload_sortie', [CaisseControllers::class,'upload_sortie']);



Route::post("rapport",[PersonnesControllers::class,'rapport']);
