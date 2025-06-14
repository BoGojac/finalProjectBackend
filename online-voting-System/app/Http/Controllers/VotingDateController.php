<?php

namespace App\Http\Controllers;

use App\Models\VotingDate;
use Illuminate\Http\Request;

class VotingDateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $votingdate = VotingDate::all();
        return response()->json([
            'message' => 'lists of voting date',
            'data' => $votingdate,
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
