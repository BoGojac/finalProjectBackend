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
Route::middleware('auth:sanctum')->post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/registerregion', [BoardManagerController::class, 'registerRegion']);
Route::middleware('auth:sanctum')->post('/partyregistration', [BoardManagerController::class, 'registerParties']);
Route::middleware('auth:sanctum')->post('/createconstituency', [BoardManagerController::class, 'createConstituency']);
Route::middleware('auth:sanctum')->post('/createpollingstation', [BoardManagerController::class, 'createPollingStation']);
Route::middleware('auth:sanctum')->post('/votingdate', [BoardManagerController::class, 'setVotingDate']);
Route::middleware('auth:sanctum')->post('/registrationdate', [BoardManagerController::class, 'setRegistrationTimeSpan']);
