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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:Users,id',
            'party_id'=> 'required|exists:Parties,id',
            'constituency_id' => 'required|exists:Constituencies,id',
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required|in:male,female',
            'registration_date' => 'required|date',
            'birth_date' => 'required|date',
            'disability' => 'required|string',
            'duration_of_residence' => 'required|string',
            'home_number' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('candidates', 'public');
        }

        $candidate = Candidate::create([
            'user_id' => $request->user_id,
            'party_id' => $request->party_id,
            'constituencies_id' => $request->constituencies_id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'registration_date' =>  $request->registration_date,
            'birth_date' =>  $request->birth_date,
            'disability' =>  $request->disability,
            'duration_of_residence' =>  $request->duration_of_residence,
            'home_number' =>  $request->home_number,
            'image' =>  $imagePath,
        ]);

         return response()->json([
            'message' => 'candidate registered successfully',
            'pollingStation' => $candidate,
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
