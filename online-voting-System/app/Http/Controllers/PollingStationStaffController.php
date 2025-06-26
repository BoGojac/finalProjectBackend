<?php

namespace App\Http\Controllers;

use App\Models\PollingStationStaff;
use Illuminate\Http\Request;

class PollingStationStaffController extends Controller
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
        'polling_station_id' => 'required|exists:polling_stations,id',
        'first_name' => 'required|string',
        'middle_name' => 'required|string',
        'last_name' => 'required|string',
        'gender' => 'required|in:male,female',
        ]);

        $pollingstationstaff = PollingStationStaff::create([
            'user_id' => $request->user_id,
            'polling_station_id' => $request->polling_station_id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
        ]);

         return response()->json([
            'message' => 'polling station staff created successfully',
            'data' => $pollingstationstaff,
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
        $staff = PollingStationStaff::where('user_id', $id)->first();

        if (!$staff) {
            return response()->json(['message' => 'Polling Station Staff not found.'], 404);
        }

        $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'gender' => 'required|in:male,female',
            'polling_station_id' => 'required|exists:polling_stations,id',
        ]);

        $staff->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'polling_station_id' => $request->polling_station_id,
        ]);

        return response()->json([
            'message' => 'Polling Station Staff updated successfully',
            'data' => $staff,
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
