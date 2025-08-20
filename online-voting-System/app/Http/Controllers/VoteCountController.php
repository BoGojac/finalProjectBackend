<?php

namespace App\Http\Controllers;

use App\Models\VoteCount;
use Illuminate\Http\Request;
use App\Models\Voter;
use Illuminate\Support\Facades\DB;

class VoteCountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        $request->validate([
            'voting_date_id' => 'required|exists:voting_dates,id',
            'candidate_id' => 'required|exists:candidates,id',
            'voter_id' => 'required|exists:voters,id',
        ]);

        // Wrap in transaction to ensure atomicity
        DB::beginTransaction();

        try {
            // Store vote
            $voteCount = VoteCount::create([
                'voting_date_id' => $request->voting_date_id,
                'candidate_id' => $request->candidate_id,
                'voter_id' => $request->voter_id,
            ]);

            // Update voter's status
            $voter = Voter::findOrFail($request->voter_id);
            $voter->voting_status = 'voted';
            $voter->save();

            DB::commit();

            return response()->json([
                'message' => 'Vote cast successfully!',
                'data' => $voteCount,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to cast vote.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(VoteCount $voteCount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VoteCount $voteCount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VoteCount $voteCount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VoteCount $voteCount)
    {
        //
    }
}
