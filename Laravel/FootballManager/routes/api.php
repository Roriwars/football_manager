<?php

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

Route::get('initClubs','ClubController@init');
Route::get('clubs','ClubController@index');
Route::put('chooseClub/{idClub}','ClubController@chooseClub');
Route::get('isMainClub','ClubController@isMainClub');
Route::get('monClub','ClubController@monClub');
Route::post('getInfoClub','ClubController@getInfoClub');
Route::post('newTactique','ClubController@newTactique');


Route::get('mesJoueurs','JoueurController@mesJoueurs');

Route::get('initCompet','CompetitionController@init');
Route::get('getCompet','CompetitionController@get');
Route::get('finCompet','CompetitionController@finCompet');
Route::post('saisonSuivante','CompetitionController@saisonSuivante');

Route::get('getClassement/{idCompet}','ClassementController@getClassement');

Route::post('getMatches','MatchController@getMatches');
Route::get('getJour/{idCompet}','MatchController@getJour');
Route::post('jouerQuartTemps','MatchController@jouerQuartTemps');
Route::post('jouerMatch','MatchController@jouerMatch');
Route::post('jouerSaison','MatchController@jouerSaison');
Route::post('finMatch','MatchController@finMatchs');

Route::get('joueurContrat/{idClub}','ContratController@getJoueursAvecContrat');
Route::post('vendreJoueur','ContratController@vendreJoueur');
Route::get('joueurSansContrat','ContratController@getJoueursSansContrat');
Route::post('creerContrat','ContratController@lierJoueurContrat');
Route::get('remplirClubJoueur','ContratController@remplirClubJoueur');
