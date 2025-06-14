<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use Illuminate\Http\Request;

class VoterController extends Controller
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
            'polling_station_id' => 'required|exists:PollingStations,id',
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required|in:male,female',
            'registration_date' => 'required|date',
            'birth_date' => 'required|date',
            'disability' => 'required|string',
            'duration_of_residence' => 'required|string',
            'home_number' => 'required|string',
        ]);



        $voter = Voter::create([
            'user_id' => $request->user_id,
            'polling_station_id' => $request->polling_station_id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'registration_date' =>  $request->registration_date,
            'birth_date' =>  $request->birth_date,
            'disability' =>  $request->disability,
            'duration_of_residence' =>  $request->duration_of_residence,
            'home_number' =>  $request->home_number,
        ]);

         return response()->json([
            'message' => 'voter registered successfully',
            'data' => $voter,
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
