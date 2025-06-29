<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class VoterController extends Controller
{
    /**
     * Display a listing of voters.
     */
    public function index()
    {
        $voters = Voter::with('user')->get();

        return response()->json([
            'message' => 'List of all voters',
            'data' => $voters
        ]);
    }

    /**
     * Store a newly created voter in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateVoterRequest($request);

        // Optionally validate age (e.g., must be >= 18 years old)
        if (Carbon::parse($validated['birth_date'])->age < 18) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => ['birth_date' => ['Voter must be at least 18 years old']]
            ], 422);
        }

        // Format duration_of_residence if needed
        if (isset($validated['residence_duration']) && isset($validated['residence_unit'])) {
            $validated['duration_of_residence'] = $validated['residence_duration'] . ' ' . $validated['residence_unit'];
        }

        $validated['registration_date'] = now()->toDateString();

       $validated['voting_status'] = $request->voting_status ?? 'pending';


        $voter = Voter::create($validated);

        return response()->json([
            'message' => 'Voter registered successfully',
            'data' => $voter,
        ], 201);
    }

    /**
     * Display the specified voter.
     */
    public function show(string $id)
    {
        $voter = Voter::with('user')->find($id);

        if (!$voter) {
            return response()->json(['message' => 'Voter not found.'], 404);
        }

        return response()->json([
            'message' => 'Here is the voter you searched for',
            'data' => $voter,
        ]);
    }

    /**
     * Update the specified voter.
     */
    public function update(Request $request, string $id)
    {
        $voter = Voter::find($id);

        if (!$voter) {
            return response()->json(['message' => 'Voter not found.'], 404);
        }

        $validated = $this->validateVoterRequest($request);

        if (Carbon::parse($validated['birth_date'])->age < 18) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => ['birth_date' => ['Voter must be at least 18 years old']]
            ], 422);
        }

        if (isset($validated['residence_duration']) && isset($validated['residence_unit'])) {
            $validated['duration_of_residence'] = $validated['residence_duration'] . ' ' . $validated['residence_unit'];
        }

        $voter->update($validated);

        return response()->json([
            'message' => 'Voter information updated successfully',
            'data' => $voter,
        ]);
    }

    /**
     * Remove the specified voter.
     */
    public function destroy(string $id)
    {
        $voter = Voter::find($id);

        if (!$voter) {
            return response()->json(['message' => 'Voter not found.'], 404);
        }

        $voter->delete();

        return response()->json(['message' => 'Voter deleted successfully.']);
    }

    /**
     * Get the authenticated voter's information.
     */
    public function get_Auth_Voters()
    {
        $voter = Auth::user()->voter;

        return response()->json([
            'message'=> 'Authenticated voter information',
            'data'=> $voter,
        ]);
    }

    /**
     * Validation logic for voter requests.
     */
    protected function validateVoterRequest(Request $request): array
    {
        return $request->validate([
            'user_id' => 'required|exists:users,id',
            'polling_station_id' => 'required|exists:polling_stations,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'registration_date' => 'nullable|date', // optional override
            'birth_date' => 'required|date',
            'disability' => 'required|in:None,Visual,Hearing,Physical,Intellectual,Other',
            'disability_type' => 'nullable|required_if:disability,Other|string|max:255',
            'residence_duration' => 'required|numeric|min:0',
            'residence_unit' => 'required|in:months,years',
            'home_number' => 'nullable|string|max:255',
            'voting_status' => 'in:pending,voted',
        ]);
    }
}
