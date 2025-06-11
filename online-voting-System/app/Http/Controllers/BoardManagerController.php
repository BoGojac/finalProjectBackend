<?php

namespace App\Http\Controllers;

use App\Models\Constituency;
use App\Models\Party;
use App\Models\PollingStation;
use App\Models\Region;
use App\Models\VotingDate;
use Illuminate\Http\Request;
use App\Models\RegistrationTimeSpan;

class BoardManagerController extends Controller
{
    public function boardManagersData(Request $request)
    {

    }

    public function registerRegion(Request $request)
    {
        $request->validate([
        'name' => 'required|string',
        'abbrevation' => 'required|string',
        ]);

        $region = Region::create([
            'name' => $request->name,
            'abbrevation' => $request->abbrevation,
        ]);

         return response()->json([
            'message' => 'region registered created successfully',
            'region' => $region,
        ], 201);

    }


    public function createConstituency(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'longitude' => 'required|numeric|decimal:8',
            'latitude' => 'required|numeric|decimal:8',
            'region_abbrevation' => 'required|string',
        ]);

        $region = Region::where('abbrevation', $request->region_abbrevation)->first();

        if (!$region) {
            return response()->json([
                'message' => 'constituency with the given constituency name not found.'
            ], 404);
        }

        $constituency = Constituency::create([
            'name' => $request->name,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'region_id' => $region->id,
        ]);

         return response()->json([
            'message' => 'constituency created successfully',
            'constituency' => $constituency,
        ], 201);
    }

    public function createPollingStation(Request $request)
    {
        $request->validate([
            'constituency_name' => 'required|string',
            'name' => 'required|string',
            'longitude' => 'required|numeric|decimal:8',
            'latitude' => 'required|numeric|decimal:8',
        ]);

        $constituencyName = Constituency::where('name', $request->constituency_name)->first();

        if (!$constituencyName) {
            return response()->json([
                'message' => 'region with the given region abbrevation name not found.'
            ], 404);
        }
        $pollingstation = PollingStation::create([
            'constituencies_id' => $constituencyName->id,
            'name' => $request->name,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
        ]);

         return response()->json([
            'message' => 'pollingstation created successfully',
            'pollingStation' => $pollingstation,
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
            'votingDate' => $votingdate,
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

    public function registerParties(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'abbrevation' => 'required|string',
            'leader' => 'required|string',
            'foundation_year' => 'required|date',
            'participation_area' => 'required|in:national,regional',
            'region_abbrevation' => 'string|nullable',
            'image' => 'nullable',
        ]);

        $regionId = null;

        if ($request->participation_area === 'regional') {
            // Lookup region only if participation_area is regional
            $region = Region::where('abbrevation', $request->region_abbrevation)->first();

            if (!$region) {
                return response()->json([
                    'message' => 'Region with the given abbreviation not found.'
                ], 404);
            }

            $regionId = $region->id;
        }

        $party = Party::create([
            'name' => $request->name,
            'abbrevation' => $request->abbrevation,
            'leader' => $request->leader,
            'foundation_year' => $request->foundation_year,
            'participation_area' => $request->participation_area,
            'region_id' => $regionId,
            'image' => $request->image,
        ]);

        return response()->json([
            'message' => 'Party registered successfully',
            'party' => $party,
        ], 201);
    }



    public function overRideVoting(Request $request)
    {
        $request->validate([

        ]);

    }
}
