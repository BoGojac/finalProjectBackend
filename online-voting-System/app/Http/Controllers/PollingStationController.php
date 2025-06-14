<?php

namespace App\Http\Controllers;

use App\Models\PollingStation;
use Illuminate\Http\Request;

class PollingStationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pollingstation = PollingStation::all();
        return response()->json([
            'message' => 'here is the polling stations list',
            'data' => $pollingstation,
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $request->validate([
            'constituencies_id' => 'required|exists:constituencies,id',
            'name' => 'required|string',
            'longitude' => 'required|numeric|decimal:8',
            'latitude' => 'required|numeric|decimal:8',
            'status' => 'in:active,inactive',
        ]);

        $pollingstation = PollingStation::create([
            'constituencies_id' => $request->constituencies_id,
            'name' => $request->name,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'status' => $request->status ?? 'active',
        ]);

         return response()->json([
            'message' => 'pollingstation created successfully',
            'pollingStation' => $pollingstation,
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
        $pollingstation = PollingStation::find($id);
        $pollingstation->update($request->all());
        return response()->json([
            'message' => 'pollind station updated successfully',
            'data' =>$pollingstation,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pollingstation = PollingStation::find($id);

        if (!$pollingstation) {
            return response()->json(['message' => 'Polling station not found.'], 404);
        }

        // Delete related polling station staff first
        $pollingstation->pollingStationStaff()->delete();

        // Then delete the polling station
        $pollingstation->delete();

        return response()->json(['message' => 'Polling station and staff deleted successfully.']);
    }

    /**
     * change the status of PollingStation
     */

     public function pollingStationStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $pollingstation = PollingStation::find($id);

        if (!$pollingstation) {
            return response()->json([
                'message' => 'Polling station not found.'
            ], 404);
        }

        $pollingstation->status = $request->status;
        $pollingstation->save();

        // Update related polling station staff status
        $pollingstation->pollingStationStaff()->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Polling station and staff status updated successfully.',
            'pollingStation' => $pollingstation
        ]);
    }
}
