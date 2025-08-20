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
        $pollingStations = PollingStation::with('constituency.region')->orderBy('created_at', 'desc') ->paginate(10);;

        $data = $pollingStations->map(function ($station) {
            return [
                'id' => $station->id,
                'name' => $station->name,
                'longitude' => $station->longitude,
                'latitude' => $station->latitude,
                'status' => $station->status,
                'constituency_id' => $station->constituency?->id,
                'constituency_name' => $station->constituency?->name,
                'region_id' => $station->constituency?->region?->id, // ✅ Required for edit form
            ];
        });

        return response()->json([
            'message' => 'Polling stations list',
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $request->validate([
            'voting_date_id'=> 'required|exists:voting_dates,id',
            'constituency_id' => 'required|exists:constituencies,id',
            'name' => 'required|string',
            'longitude' => 'required|numeric|decimal:8',
            'latitude' => 'required|numeric|decimal:8',
            'status' => 'in:active,inactive',
        ]);

        $pollingstation = PollingStation::create([
            'voting_date_id' => $request->voting_date_id,
            'constituency_id' => $request->constituency_id,
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

        $pollingstation->delete();

        return response()->json(['message' => 'Polling station and associated staff deleted successfully.']);
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
