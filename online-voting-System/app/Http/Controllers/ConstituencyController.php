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
        try {
            $constituencies = Constituency::with('region.voting_date')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'message' => 'Constituencies with regions and voting dates',
                'data' => $constituencies
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch data',
                'details' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'voting_date_id' => 'required|exists:voting_dates,id',
            'name' => 'required|string',
            'longitude' => 'required|numeric|decimal:8',
            'latitude' => 'required|numeric|decimal:8',
            'region_id' => 'string|exists:regions,id',
            'status' => 'in:active,inactive',

        ]);

        // Get constituency_id from the user
        $constituency = Constituency::create([
            'voting_date_id' => $request->voting_date_id,
            'name' => $request->name,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'region_id' => $request->region_id,
            'status' => $request->status ?? 'active',
        ]);

         return response()->json([
            'message' => 'constituency created successfully',
            'constituency' => $constituency,
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
