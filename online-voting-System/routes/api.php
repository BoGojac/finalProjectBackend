<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardManagerController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\ConstituencyController;
use App\Http\Controllers\ConstituencyStaffController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\PollingStationController;
use App\Http\Controllers\PollingStationStaffController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RegistrationTimeSpanController;
use App\Http\Controllers\VoterController;
use App\Http\Controllers\VotingDateController;


/** User End Point */

Route::post('/login', [AuthController::class, 'login']);
Route::get('/constituencystaff-user', [ConstituencyStaffController::class, 'get_Auth_Candidate'])->middleware('auth:sanctum');

// Route::middleware('auth:sanctum')->group( function () {

    /** User End Point */
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/userregister', [AuthController::class, 'register']);
Route::get('/user', [AuthController::class, 'getUser']);
Route::put('/user/{id}', [AuthController::class, 'update']);
Route::patch('/user/status/{id}', [AuthController::class, 'toggleStatus']);



/** Admin End Point */

Route::get('/admin', [AdminController::class, 'index']);
Route::post('/admin',[AdminController::class, 'store']);
Route::put('/admin/{id}', [AdminController::class, 'update']);
Route::delete('/admin/{id}', [AdminController::class, 'destroy']);

/** Board Manager End Point */
Route::get('/boardmanagers', [BoardManagerController::class, 'index']);
Route::get('/boardmanagers/{id}', [BoardManagerController::class, 'update']);
Route::post('/boardmanagers',[BoardManagerController::class, 'store']);
Route::put('/boardmanagers/{id}', [BoardManagerController::class, 'update']);
Route::delete('/boardmanagers/{id}', [BoardManagerController::class, 'destroy']);

/** Constituency Staff End Point */

Route::get('/constituencystaff', [ConstituencyStaffController::class, 'index']);
Route::get('/constituencystaff/{id}', [ConstituencyStaffController::class, 'show']);
Route::post('/constituencystaff',[ConstituencyStaffController::class, 'store']);
Route::put('/constituencystaff/{id}', [ConstituencyStaffController::class, 'update']);
Route::delete('/constituencystaff/{id}', [ConstituencyStaffController::class, 'destroy']);


/** Polling Station Staff End Point */
Route::get('/pollingstationstaff', [PollingStationStaffController::class, 'index']);
Route::post('/pollingstationstaff',[PollingStationStaffController::class, 'store']);
Route::put('/pollingstationstaff/{id}', [PollingStationStaffController::class, 'update']);
Route::delete('/pollingstationstaff/{id}', [PollingStationStaffController::class, 'destroy']);

/** Region End Point */
Route::post('/regions', [RegionController::class, 'store']);
Route::get('/regions', [RegionController::class, 'index']);
Route::put('/regions/{id}', [RegionController::class, 'update']);
Route::delete('/regions/{id}', [RegionController::class, 'destroy']);


/** constituency end points */
Route::get('/constituency', [ConstituencyController::class, 'index']);
Route::get('/constituency/user/{user_id}', [ConstituencyController::class, 'getConstituencyByUserId']);
Route::post('/constituency',[ConstituencyController::class, 'store']);
Route::put('/constituency/{id}', [ConstituencyController::class, 'update']);
Route::delete('/constituency/{id}', [ConstituencyController::class, 'destroy']);
Route::patch('/constituency/status/{id}', [ConstituencyController::class, 'constituencyStatus']);

/** Polling Station End Point */

Route::post('/pollingstation', [PollingStationController::class, 'store']);
Route::get('/pollingstation', [PollingStationController::class, 'index']);
Route::put('/pollingstation/{id}', [PollingStationController::class, 'update']);
Route::delete('/pollingstation/{id}', [PollingStationController::class, 'destroy']);
Route::patch('/pollingstation/status/{id}', [PollingStationController::class, 'pollingStationStatus']);

/** Party End Point */
Route::post('/party', [PartyController::class, 'store']);
Route::get('/party', [PartyController::class, 'index']);
Route::put('/party/{id}', [PartyController::class, 'update']);
Route::delete('/party/{id}', [PartyController::class, 'destroy']);
Route::patch('/party/status/{id}', [PartyController::class, 'partyStatus']);


/** Candidate End Point */

Route::get('/candidate', [CandidateController::class, 'index']);
Route::post('/candidate',[CandidateController::class, 'store']);
Route::put('/candidate/{id}', [CandidateController::class, 'update']);
Route::delete('/candidate/{id}', [CandidateController::class, 'destroy']);


/** Voter End Point */

Route::get('/voter', [VoterController::class, 'index']);
Route::post('/voter',[VoterController::class, 'store']);
Route::put('/voter/{id}', [VoterController::class, 'update']);
Route::delete('/voter/{id}', [VoterController::class, 'destroy']);

/** Voting Date End Point */
Route::post('/voting-date', [VotingDateController::class, 'store']);
Route::get('/voting-date', [VotingDateController::class, 'index']);              // All
Route::get('/voting-date/{id}', [VotingDateController::class, 'show']);
Route::put('/voting-date/{id}', [VotingDateController::class, 'update']);       // By ID
Route::delete('/voting-date/{id}', [VotingDateController::class, 'destroy']);



/** Registration TimeSpan End Point */
Route::post('/registration-time-span', [RegistrationTimeSpanController::class, 'store']);
Route::get('/registration-time-span', [RegistrationTimeSpanController::class, 'index']);
Route::put('/registration-time-span/{id}', [RegistrationTimeSpanController::class, 'update']);
Route::delete('/registration-time-span/{id}', [RegistrationTimeSpanController::class, 'destroy']);


/** Override End Point */
Route::put('/voting/override', [BoardManagerController::class, 'overRideVoting']);

// });
