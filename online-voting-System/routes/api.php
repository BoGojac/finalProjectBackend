<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardManagerController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->post('/userregister', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/boardmanagersdata', [BoardManagerController::class, 'boardManagersData']);


Route::middleware('auth:sanctum')->post('/registerregion', [BoardManagerController::class, 'registerRegion']);
Route::get('/regions', [BoardManagerController::class, 'getRegisteredRegions']);
Route::middleware('auth:sanctum')->put('/region/{id}', [BoardManagerController::class, 'updateRegisteredRegions']);
Route::middleware('auth:sanctum')->delete('/region/{id}', [BoardManagerController::class, 'deleteRegisteredRegions']);


Route::middleware('auth:sanctum')->post('/partyregistration', [BoardManagerController::class, 'registerParties']);
Route::get('/parties', [BoardManagerController::class, 'getParty']);
Route::middleware('auth:sanctum')->put('/party/{id}', [BoardManagerController::class, 'updateParty']);
Route::middleware('auth:sanctum')->patch('/party/status/{id}', [BoardManagerController::class, 'partyStatus']);
Route::middleware('auth:sanctum')->delete('/party/{id}', [BoardManagerController::class, 'deleteParty']);


Route::middleware('auth:sanctum')->post('/createconstituency', [BoardManagerController::class, 'createConstituency']);
Route::get('/get-constituencies', [BoardManagerController::class, 'getConstituency']);
Route::middleware('auth:sanctum')->put('/update-constituency/{id}', [BoardManagerController::class, 'updateConstituency']);
Route::middleware('auth:sanctum')->delete('/delete-constituency/{id}', [BoardManagerController::class, 'deleteConstituency']);


Route::middleware('auth:sanctum')->post('/createpollingstation', [BoardManagerController::class, 'createPollingStation']);
Route::get('/get-polling-stations', [BoardManagerController::class, 'getPollingStation']);
Route::middleware('auth:sanctum')->put('/update-polling-station/{id}', [BoardManagerController::class, 'updatePollingStation']);
Route::middleware('auth:sanctum')->delete('/delete-polling-station/{id}', [BoardManagerController::class, 'deletePollingStation']);


Route::middleware('auth:sanctum')->post('/votingdate', [BoardManagerController::class, 'setVotingDate']);
Route::get('/voting-dates', [BoardManagerController::class, 'getVotingDates']);              // All
Route::get('/voting-date/{id}', [BoardManagerController::class, 'getVotingDateById']);       // By ID
Route::post('/voting-date/by-title', [BoardManagerController::class, 'getVotingDateByTitle']);
Route::middleware('auth:sanctum')->put('/voting-date/{id}', [BoardManagerController::class, 'updateVotingDate']);

Route::middleware('auth:sanctum')->post('/registrationdate', [BoardManagerController::class, 'setRegistrationTimeSpan']);
Route::get('/registration-time-span', [BoardManagerController::class, 'getRegistrationTimeSpan']);     // Read (by title)
Route::middleware('auth:sanctum')->put('/registration-time-span/{id}', [BoardManagerController::class, 'updateRegistrationTimeSpan']); // Update
Route::middleware('auth:sanctum')->delete('/registration-time-span/{id}', [BoardManagerController::class, 'deleteRegistrationTimeSpan']);

Route::middleware('auth:sanctum')->put('/voting/override', [BoardManagerController::class, 'overRideVoting']);

