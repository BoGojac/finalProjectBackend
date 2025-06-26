<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Models\Constituency;
use App\Models\Region;
use App\Models\User;

class ConstituencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $constituency = Constituency::all();
        return response()->json([
            'message' => 'here is the constituencies',
            'data' => $constituency
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'party_id' => 'nullable|exists:parties,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'birth_date' => 'required|date|before_or_equal:-21 years',
            'disability' => 'required|in:None,Visual,Hearing,Physical,Intellectual,Other',
            'disability_type' => 'nullable|string|max:255',
            'residence_duration' => 'required|numeric|min:0',
            'residence_unit' => 'required|in:months,years',
            'home_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'candidate_type' => 'required|in:individual,party',
            'voting_date_id' => 'required|exists:voting_dates,id',
        ]);

        // Get constituency_id from the user
        $user = User::find($validated['user_id']);
        if (!$user || !$user->constituency_id) {
            return response()->json([
                'message' => 'User constituency information is missing',
                'errors' => ['constituency' => ['Constituency information is missing for this user']]
            ], 422);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $validated['original_image_name'] = $image->getClientOriginalName();
            $validated['image'] = $image->store('candidates', 'public');
        }

        // Combine residence duration and unit
        $validated['duration_of_residence'] = $validated['residence_duration'] . ' ' . $validated['residence_unit'];

        // Set registration date to today
        $validated['registration_date'] = now()->format('Y-m-d');

        // Add constituency_id from user
        $validated['constituency_id'] = $user->constituency_id;

        $candidate = Candidate::create($validated);

        return response()->json([
            'message' => 'Candidate registered successfully',
            'data' => $candidate,
        ], 201);
    }


    /**
     * Get constituency by user_id
     */


     public function getConstituencyByUserId($user_id)
    {
        $user = User::with('constituency')->find($user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!$user->constituency) {
            return response()->json(['message' => 'Constituency not found for this user'], 404);
        }

        return response()->json([
            'constituency_id' => $user->constituency->id,
            'constituency_name' => $user->constituency->name
        ]);
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
        $constituency = Constituency::find($id);
        $constituency->update($request->all());
        return response()->json([
            'message' => 'constituency successfully updated',
            'data' => $constituency,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(string $id)
    {
        $constituency = Constituency::find($id);

        if (!$constituency) {
            return response()->json(['message' => 'Constituency not found.'], 404);
        }

        $constituency->delete();

        return response()->json(['message' => 'Constituency and all related records deleted successfully.']);
    }

    /**
     * change the status of constituency
     */

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

}
