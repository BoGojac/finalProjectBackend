<?php

namespace App\Http\Controllers;

use App\Models\RegistrationTimeSpan;
use App\Models\VotingDate;
use Illuminate\Http\Request;

class RegistrationTimeSpanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RegistrationTimeSpan::query();

        // Filter by voting_date_id if provided
        if ($request->has('voting_date_id')) {
            $query->where('voting_date_id', $request->input('voting_date_id'));
        }

        // Eager load voting date relationship if requested
        if ($request->has('include_dates') && $request->input('include_dates')) {
            $query->with('votingDate');
        }

        $registrationPeriods = $query->get();

        return response()->json([
            'data' => $registrationPeriods
        ]);
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

        // Fetch the voting date
        $votingDate = VotingDate::find($request->voting_date_id);
        if (!$votingDate) {
            return response()->json(['message' => 'Voting date not found.'], 404);
        }

        // Convert to Carbon for date comparisons
        $endingDate = \Carbon\Carbon::parse($request->ending_date);
        $votingDateCarbon = \Carbon\Carbon::parse($votingDate->date); // Assuming the column is `date`

        // ✅ Ensure the registration ends before the voting date
        if ($endingDate->gte($votingDateCarbon)) {
            return response()->json([
                'message' => 'Registration must end before the voting date.'
            ], 422);
        }

        // ✅ If it's a candidate registration, ensure it ends at least 3 months before the voting date
        if ($request->type === 'candidate') {
            $minimumEndDate = $votingDateCarbon->copy()->subMonths(3);
            if ($endingDate->gt($minimumEndDate)) {
                return response()->json([
                    'message' => 'Candidate registration must end at least 3 months before the voting date.'
                ], 422);
            }
        }

        // Prevent duplicate registration time spans for same voting_date + type
        $exists = RegistrationTimeSpan::where('voting_date_id', $request->voting_date_id)
            ->where('type', $request->type)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => "A {$request->type} registration period is already set for this voting date."
            ], 409);
        }

        // Save the record
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
