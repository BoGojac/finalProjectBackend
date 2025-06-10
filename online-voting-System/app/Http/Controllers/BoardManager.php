<?php

namespace App\Http\Controllers;

use App\Models\Constituency;
use App\Models\PollingStation;
use App\Models\VotingDate;
use Illuminate\Http\Request;
use App\Models\RegistrationTimeSpan;

class BoardManager extends Controller
{
    public function boardManagersData(Request $request)
    {

    }

    public function createConstituency(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'longitude' => 'required|numeric|decimal:8',
            'latitude' => 'required|numeric|decimal:8',
            'region' => 'required|string',
        ]);

        $constituency = Constituency::create([
            'name' => $request->name,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'region' => $request->region,
        ]);

         return response()->json([
            'message' => 'constituency created successfully',
            'user' => $constituency,
        ], 201);
    }

    public function createPollingStation(Request $request)
    {
        $request->validate([
            'constituency_name' => 'required|string',
            'name' => 'required|string',
            'longitude' => 'required|numeric|decimal:8',
            'latitude' => 'required|numeric|decimal:8',
            'region' => 'required|string',
        ]);

        $constituencyName = Constituency::where('name', $request->constituency_name)->first();

        if (!$constituencyName) {
            return response()->json([
                'message' => 'constituency with the given constituency name not found.'
            ], 404);
        }
        $pollingstation = PollingStation::create([
            'constituencies_id' => $constituencyName->id,
            'name' => $request->name,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'region' => $request->region,
        ]);

         return response()->json([
            'message' => 'pollingstation created successfully',
            'user' => $pollingstation,
        ], 201);

    }

    public function setVotingDate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'voting_date' => 'required|date'
        ]);

        $votingdate = VotingDate::create([
            'title' => $request->title,
            'voting_date' => $request->voting_date,
        ]);

         return response()->json([
            'message' => 'voting date setted successfully',
            'user' => $votingdate,
        ], 201);

    }



    public function setRegistrationTimeSpan(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'beginning_date' => 'required|date',
            'ending_date' => 'required|date'
        ]);

        // Find the voting date entry by title
        $votingDate = VotingDate::where('title', $request->title)->first();

        if (!$votingDate) {
            return response()->json([
                'message' => 'Voting date with the given title not found.'
            ], 404);
        }

        // Create the registration time span record
        $registrationTimeSpan = RegistrationTimeSpan::create([
            'voting_date_id' => $votingDate->id,
            'beginning_date' => $request->beginning_date,
            'ending_date' => $request->ending_date
        ]);

        return response()->json([
            'message' => 'Registration time span set successfully.',
            'data' => $registrationTimeSpan,
        ], 201);
    }


    public function overRideVoting(Request $request)
    {

    }
}
