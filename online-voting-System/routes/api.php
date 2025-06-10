<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardManager;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/createconstituency', [BoardManager::class, 'createConstituency']);
Route::middleware('auth:sanctum')->post('/createpollingstation', [BoardManager::class, 'createPollingStation']);
Route::middleware('auth:sanctum')->post('/votingdate', [BoardManager::class, 'setVotingDate']);
Route::middleware('auth:sanctum')->post('/registrationdate', [BoardManager::class, 'setRegistrationTimeSpan']);
