<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $region = Region::all();
        return response()->json([
            'message' => 'here is the regions',
            'data' => $region
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'name' => 'required|string',
        'abbrevation' => 'required|string',
        ]);

        $region = Region::create([
            'name' => $request->name,
            'abbrevation' => $request->abbrevation,
        ]);

         return response()->json([
            'message' => 'region registered created successfully',
            'region' => $region,
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
        $region = Region::find($id);
        $region->update($request->all());
        return response()->json([
            'message' => 'successfully updated region',
            'data'=> $region
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $region = Region::find($id);

        if (!$region) {
            return response()->json(['message' => 'Region not found.'], 404);
        }

        // Delete related parties and their candidates
        foreach ($region->parties as $party) {
            $party->candidates()->delete();
            $party->delete();
        }

        // Delete related constituencies and their resources
        foreach ($region->constituencies as $constituency) {
            foreach ($constituency->pollingStations as $station) {
                $station->pollingStationStaff()->delete();
                $station->delete();
            }

            $constituency->constituencyStaff()->delete();
            $constituency->delete();
        }

        // Finally, delete the region
        $region->delete();

        return response()->json(['message' => 'Region and all related records deleted successfully.']);
    }
}
