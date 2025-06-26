<?php

namespace App\Http\Controllers;

use App\Models\RegistrationTimeSpan;
use Illuminate\Http\Request;

class RegistrationTimeSpanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
            'type' => 'required|in:voter,candidate',
            'beginning_date' => 'required|date',
            'ending_date' => 'required|date|after_or_equal:beginning_date',
        ]);

        // Check if a record already exists for the type + voting date
        $exists = RegistrationTimeSpan::where('voting_date_id', $request->voting_date_id)
            ->where('type', $request->type)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => "A {$request->type} registration period is already set for this voting date."
            ], 409);
        }

        $registrationTimeSpan = RegistrationTimeSpan::create($request->only([
            'voting_date_id',
            'type',
            'beginning_date',
            'ending_date'
        ]));

        return response()->json([
            'message' => 'Registration time span set successfully.',
            'data' => $registrationTimeSpan,
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'beginning_date' => 'required|date',
            'ending_date' => 'required|date|after_or_equal:beginning_date',
        ]);

        $timeSpan = RegistrationTimeSpan::find($id);

        if (!$timeSpan) {
            return response()->json(['message' => 'Registration time span not found.'], 404);
        }

        $timeSpan->update([
            'beginning_date' => $request->beginning_date,
            'ending_date' => $request->ending_date,
        ]);

        return response()->json([
            'message' => 'Registration time span updated successfully.',
            'data' => $timeSpan,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $timeSpan = RegistrationTimeSpan::find($id);

        if (!$timeSpan) {
            return response()->json(['message' => 'Registration time span not found.'], 404);
        }

        $timeSpan->delete();

        return response()->json(['message' => 'Registration time span deleted successfully.']);
    }
}
