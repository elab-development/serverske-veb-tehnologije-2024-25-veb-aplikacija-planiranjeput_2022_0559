<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\ExternalAdvisorsController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ReviewController;
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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/destinations', [DestinationController::class, 'index']);
Route::get('/destinations/{destination}', [DestinationController::class, 'show']);

Route::get('/places', [PlaceController::class, 'index']);
Route::get('/places/{place}/reviews', [ReviewController::class, 'index']);
Route::get('/places/{place}', [PlaceController::class, 'show']);

Route::get('/tripadvisor16/hotels/search', [ExternalAdvisorsController::class, 'searchHotelsByLocation16']);
Route::get('/tripadvisor-com1/attractions/search', [ExternalAdvisorsController::class, 'attractionsByQueryCom1']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::resource('destinations', DestinationController::class)
        ->only(['store', 'update', 'destroy']);

    Route::resource('places', PlaceController::class)
        ->only(['store', 'update', 'destroy']);

    Route::resource('reviews', ReviewController::class)
        ->only(['store', 'destroy']);
});