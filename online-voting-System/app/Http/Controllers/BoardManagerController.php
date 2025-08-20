<?php

namespace App\Http\Controllers;

use App\Models\BoardManager;
use App\Models\OverrideHistory;
use App\Models\Constituency;
use App\Models\PollingStation;
use App\Models\Candidate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\Cast\String_;

class BoardManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $boardmanager = BoardManager::all();
        return response()->json([
            'message' => 'information of board managers',
            'data' => $boardmanager,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $request->validate([
            'user_id' => 'required|exists:Users,id',
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required|in:Male,Female',
        ]);


        $boardmanager = BoardManager::create([
            'user_id' => $request->user_id,
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       $boardmanager = BoardManager::find($id);
        return response()->json([
            'message' =>  'this is the constituency staff you search for',
            'data' => $boardmanager,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find by user_id
        $boardmanager = BoardManager::where('user_id', $id)->first();

        if (!$boardmanager) {
            return response()->json(['message' => 'Boardmanager not found.'], 404);
        }

        $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'gender' =>  'required|in:Male,Female',
        ]);

        $boardmanager->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
        ]);

        return response()->json([
            'message' => 'Boardmanager information updated successfully',
            'data' => $boardmanager,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $boardmanager = BoardManager::find($id);

        if (!$boardmanager) {
            return response()->json(['message' => 'BoardManager not found.'], 404);
        }

        // Delete the party
        $boardmanager->delete();

        return response()->json(['message' => 'Boardmanager deleted successfully.']);
    }

    /**
     * get authenticated board manager
     */
    public function get_Auth_BoardManager()
    {
        $user = Auth::user();
        $board_manager = $user->board_manager;
        return response()->json([
            'message'=> 'this the boardmanager staff loged in',
            'data'=> [
                'user' => $user,
                'board_manager' => $board_manager,
            ],
        ]);
    }

    /**
     *  Voter Override Method
     */

     public function overrideVoting(Request $request)
    {
        $request->validate([
            'voting_date_id' => 'required|exists:voting_dates,id',
            'level' => 'required|in:entire,constituency,pollingstation',
            'target_id' => 'nullable|integer'
        ]);

        $user = Auth::user(); // ✅ Authenticated user
        $userId = $user->id;
        $level = $request->level;
        $targetId = $request->target_id;

        if ($level === 'entire') {
            foreach (Constituency::all() as $constituency) {
                $constituency->update(['status' => 'inactive']);
                $constituency->pollingStations()->update(['status' => 'inactive']);
                Candidate::where('constituency_id', $constituency->id)->update(['status' => 'inactive']);
            }

            OverrideHistory::create([
                'voting_date_id' => $request->voting_date_id,
                'user_id' => $userId,
                'override_level' => 'entire',
            ]);

            return response()->json(['message' => 'Voting overridden entirely.']);
        }

        if ($level === 'constituency') {
            $constituency = Constituency::findOrFail($targetId);
            $constituency->update(['status' => 'inactive']);
            $constituency->pollingStations()->update(['status' => 'inactive']);
            Candidate::where('constituency_id', $constituency->id)->update(['status' => 'inactive']);

            OverrideHistory::create([
                'voting_date_id' => $request->voting_date_id,
                'user_id' => $userId,
                'override_level' => 'constituency',
                'constituency_id' => $targetId,
            ]);

            return response()->json(['message' => 'Voting overridden for selected constituency.']);
        }

        if ($level === 'pollingstation') {
            $station = PollingStation::findOrFail($targetId);
            $station->update(['status' => 'inactive']);

            OverrideHistory::create([
                'voting_date_id' => $request->voting_date_id,
                'user_id' => $userId,
                'override_level' => 'pollingstation',
                'polling_station_id' => $targetId,
            ]);

            return response()->json(['message' => 'Voting overridden for selected polling station.']);
        }
    }

    /**
     *  Roll Back THe Override Vote
     */
    public function rollbackOverrideVoting(Request $request)
    {
        $request->validate([
            'level' => 'required|in:entire,constituency,pollingstation',
            'target_id' => 'nullable|integer',
            'substitution_date' => 'required|date|after:today',
        ]);

        $user = Auth::user(); // ✅ Authenticated user
        $userId = $user->id;
        $level = $request->level;
        $targetId = $request->target_id;
        $substitutionDate = $request->substitution_date;

        $query = OverrideHistory::where('override_level', $level)
                                ->where('rollback_status', false);

        if ($level === 'entire') {
            foreach (Constituency::all() as $constituency) {
                $constituency->update(['status' => 'active']);
                $constituency->pollingStations()->update(['status' => 'active']);
                Candidate::where('constituency_id', $constituency->id)->update(['status' => 'active']);
            }
        } elseif ($level === 'constituency') {
            $constituency = Constituency::findOrFail($targetId);
            $constituency->update(['status' => 'active']);
            $constituency->pollingStations()->update(['status' => 'active']);
            Candidate::where('constituency_id', $constituency->id)->update(['status' => 'active']);
            $query->where('constituency_id', $targetId);
        } elseif ($level === 'pollingstation') {
            $station = PollingStation::findOrFail($targetId);
            $station->update(['status' => 'active']);
            $query->where('polling_station_id', $targetId);
        }

        $override = $query->latest()->first();

        if (!$override) {
            return response()->json(['message' => 'No override record found to rollback.'], 404);
        }

        $override->update([
            'rollback_status' => true,
            'rollback_user_id' => $userId,
            'rollback_date' => now(),
            'substitution_date' => $substitutionDate,
        ]);

        return response()->json(['message' => 'Rollback completed. Voting rescheduled.']);
    }


}
