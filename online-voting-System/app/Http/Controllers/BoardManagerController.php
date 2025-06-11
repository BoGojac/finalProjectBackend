<?php

namespace App\Http\Controllers;

use App\Models\BoardManager;
use App\Models\Constituency;
use App\Models\Party;
use App\Models\PollingStation;
use App\Models\Region;
use App\Models\VotingDate;
use Illuminate\Http\Request;
use App\Models\RegistrationTimeSpan;
use App\Models\User;

class BoardManagerController extends Controller
{
    public function boardManagersData(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required|in:male,female',
        ]);

        $user = User::where('username', $request->user_name)->first();

        if (!$user) {
            return response()->json([
                'message' => 'user with the given username not found.'
            ], 404);
        }

        $boardmanager = BoardManager::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
        ]);

         return response()->json([
            'message' => 'boardmanager created successfully',
            'boardmanager' => $boardmanager,
        ], 201);

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

    public function getRegisteredRegions(Request $request)
    {
        return Region:: all();
    }

    public function updateRegisteredRegions(Request $request, string $id)
    {
        $region = Region::find($id);
        $region->update($request->all());
        return response()->json($region);
    }

    public function deleteRegisteredRegions(Request $request, string $id)
    {
        $region = Region::find($id);

        if (!$region) {
            return response()->json(['message' => 'Region not found.'], 404);
        }

        // Delete related parties and their candidates
        foreach ($region->parties as $party) {
            $party->candidates()->delete();
            $party->delete();
        }

        // Delete related constituencies and their resources
        foreach ($region->constituencies as $constituency) {
            foreach ($constituency->pollingStations as $station) {
                $station->pollingStationStaff()->delete();
                $station->delete();
            }

            $constituency->constituencyStaff()->delete();
            $constituency->delete();
        }

        // Finally, delete the region
        $region->delete();

        return response()->json(['message' => 'Region and all related records deleted successfully.']);
    }



    public function createConstituency(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'longitude' => 'required|numeric|decimal:8',
            'latitude' => 'required|numeric|decimal:8',
            'region_abbrevation' => 'required|string',
            // 'status' => 'required|in:active,inactive',
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
            // 'status' => $request->status,
        ]);

         return response()->json([
            'message' => 'constituency created successfully',
            'constituency' => $constituency,
        ], 201);
    }


    public function getConstituency(Request $request)
    {
        return Constituency:: all();
    }

    public function updateConstituency(Request $request, string $id)
    {
        $constituency = Constituency::find($id);
        $constituency->update($request->all());
        return response()->json($constituency);
    }

    public function constituencyStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $constituency = Constituency::find($id);

        if (!$constituency) {
            return response()->json([
                'message' => 'Constituency not found.'
            ], 404);
        }

        $constituency->status = $request->status;
        $constituency->save();

        // Update constituencyStaff
        $constituency->constituencyStaff()->update(['status' => $request->status]);

        // Update polling stations and their staff
        foreach ($constituency->pollingStations as $station) {
            $station->status = $request->status;
            $station->save();

            $station->pollingStationStaff()->update(['status' => $request->status]);
        }

        return response()->json([
            'message' => 'Constituency status and related records updated successfully.',
            'constituency' => $constituency
        ]);
    }



    public function deleteConstituency(Request $request, string $id)
    {
        $constituency = Constituency::find($id);

        if (!$constituency) {
            return response()->json(['message' => 'Constituency not found.'], 404);
        }

        // Delete polling stations and their staff
        foreach ($constituency->pollingStations as $station) {
            $station->pollingStationStaff()->delete(); // delete staff first
            $station->delete();                        // then polling station
        }

        // Delete constituency staff
        $constituency->constituencyStaff()->delete();

        // Finally, delete the constituency
        $constituency->delete();

        return response()->json(['message' => 'Constituency and all related records deleted successfully.']);
    }



    public function createPollingStation(Request $request)
    {
        $request->validate([
            'constituency_name' => 'required|string',
            'name' => 'required|string',
            'longitude' => 'required|numeric|decimal:8',
            'latitude' => 'required|numeric|decimal:8',
            // 'status' => 'required|in:active,inactive',
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
            // 'status' => $request->status,
        ]);

         return response()->json([
            'message' => 'pollingstation created successfully',
            'pollingStation' => $pollingstation,
        ], 201);

    }

    public function getPollingStation(Request $request)
    {
        return PollingStation:: all();
    }

    public function updatePollingStation(Request $request, string $id)
    {
        $pollingstation = PollingStation::find($id);
        $pollingstation->update($request->all());
        return response()->json($pollingstation);
    }

   public function pollingStationStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $pollingstation = PollingStation::find($id);

        if (!$pollingstation) {
            return response()->json([
                'message' => 'Polling station not found.'
            ], 404);
        }

        $pollingstation->status = $request->status;
        $pollingstation->save();

        // Update related polling station staff status
        $pollingstation->pollingStationStaff()->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Polling station and staff status updated successfully.',
            'pollingStation' => $pollingstation
        ]);
    }


    public function deletePollingStation(Request $request, string $id)
    {
        $pollingstation = PollingStation::find($id);

        if (!$pollingstation) {
            return response()->json(['message' => 'Polling station not found.'], 404);
        }

        // Delete related polling station staff first
        $pollingstation->pollingStationStaff()->delete();

        // Then delete the polling station
        $pollingstation->delete();

        return response()->json(['message' => 'Polling station and staff deleted successfully.']);
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

    public function getVotingDates()
    {
        $votingDates = VotingDate::all();

        return response()->json([
            'message' => 'Voting dates retrieved successfully.',
            'data' => $votingDates
        ]);
    }

    public function getVotingDateById(string $id)
    {
        $votingDate = VotingDate::find($id);

        if (!$votingDate) {
            return response()->json(['message' => 'Voting date not found.'], 404);
        }

        return response()->json([
            'message' => 'Voting date retrieved successfully.',
            'data' => $votingDate
        ]);
    }

    public function getVotingDateByTitle(Request $request)
    {
        $request->validate([
            'title' => 'required|string'
        ]);

        $votingDate = VotingDate::where('title', $request->title)->first();

        if (!$votingDate) {
            return response()->json(['message' => 'Voting date not found.'], 404);
        }

        return response()->json([
            'message' => 'Voting date retrieved successfully.',
            'data' => $votingDate
        ]);
    }


    public function updateVotingDate(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string',
            'voting_date' => 'required|date'
        ]);

        $votingDate = VotingDate::find($id);

        if (!$votingDate) {
            return response()->json(['message' => 'Voting date not found.'], 404);
        }

        $votingDate->update([
            'title' => $request->title,
            'voting_date' => $request->voting_date
        ]);

        return response()->json([
            'message' => 'Voting date updated successfully.',
            'votingDate' => $votingDate,
        ]);
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

    public function getRegistrationTimeSpan(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
        ]);

        $votingDate = VotingDate::where('title', $request->title)->first();

        if (!$votingDate) {
            return response()->json(['message' => 'Voting date not found.'], 404);
        }

        $timeSpan = RegistrationTimeSpan::where('voting_date_id', $votingDate->id)->first();

        if (!$timeSpan) {
            return response()->json(['message' => 'Registration time span not set.'], 404);
        }

        return response()->json([
            'message' => 'Registration time span retrieved successfully.',
            'data' => $timeSpan,
        ]);
    }

    public function updateRegistrationTimeSpan(Request $request, string $id)
    {
        $request->validate([
            'beginning_date' => 'required|date',
            'ending_date' => 'required|date',
        ]);

        $timeSpan = RegistrationTimeSpan::find($id);

        if (!$timeSpan) {
            return response()->json(['message' => 'Registration time span not found.'], 404);
        }

        $timeSpan->update([
            'beginning_date' => $request->beginning_date,
            'ending_date' => $request->ending_date
        ]);

        return response()->json([
            'message' => 'Registration time span updated successfully.',
            'data' => $timeSpan,
        ]);
    }

    public function deleteRegistrationTimeSpan(Request $request, string $id)
    {
        $timeSpan = RegistrationTimeSpan::find($id);

        if (!$timeSpan) {
            return response()->json(['message' => 'Registration time span not found.'], 404);
        }

        $timeSpan->delete();

        return response()->json(['message' => 'Registration time span deleted successfully.']);
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
            // 'status' => 'required|in:active,inactive',
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


    public function getParty(Request $request)
    {
        return Party:: all();
    }

    public function updateParty(Request $request, string $id)
    {
        $party = Party::find($id);

        if (!$party) {
            return response()->json(['message' => 'Party not found.'], 404);
        }

        $party->update($request->all());

        return response()->json($party);
    }


    public function partyStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $party = Party::find($id);

        if (!$party) {
            return response()->json(['message' => 'Party not found.'], 404);
        }

        $party->status = $request->status;
        $party->save();

        // Update all related candidates
        $party->candidates()->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Party and related candidates status updated successfully.',
            'party' => $party
        ]);
    }



    public function deleteParty(Request $request, string $id)
    {
        $party = Party::find($id);

        if (!$party) {
            return response()->json(['message' => 'Party not found.'], 404);
        }

        // Delete related candidates
        $party->candidates()->delete();

        // Delete the party
        $party->delete();

        return response()->json(['message' => 'Party and related candidates deleted successfully.']);
    }



    public function overRideVoting(Request $request)
    {
        $request->validate([

        ]);

    }
}
