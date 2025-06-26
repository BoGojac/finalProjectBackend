<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $candidates = Candidate::all();
        return response()->json($candidates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'party_id' => 'nullable|required_if:candidate_type,party|exists:parties,id',
            'constituency_id' => 'required|exists:constituencies,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'birth_date' => 'required|date|before_or_equal:-21 years',
            'disability' => 'required|in:None,Visual,Hearing,Physical,Intellectual,Other',
            'disability_type' => 'nullable|required_if:disability,Other|string|max:255',
            'residence_duration' => 'required|numeric|min:0',
            'residence_unit' => 'required|in:months,years',
            'home_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'candidate_type' => 'required|in:individual,party',
        ]);

        // Validate residence duration based on unit
        if ($validated['residence_unit'] === 'months' && $validated['residence_duration'] < 6) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => [
                    'residence_duration' => ['Must be at least 6 months when unit is months']
                ]
            ], 422);
        }

        if ($validated['residence_unit'] === 'years' && $validated['residence_duration'] < 1) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => [
                    'residence_duration' => ['Must be at least 1 year when unit is years']
                ]
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

        // Create candidate
        $candidate = Candidate::create($validated);

        return response()->json([
            'message' => 'Candidate registered successfully',
            'data' => $candidate,
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
        $candidate = Candidate:: find($id);

        if (!$candidate) {
            return response()->json(['message' => 'Candidate not found.'], 404);
        }

        $candidate->update($request->all());

        return response()->json([
            'message' => 'party information updated successfully',
            'data' => $candidate,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
