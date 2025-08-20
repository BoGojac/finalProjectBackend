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
        $region = Region::orderBy('created_at', 'desc') ->paginate(10);
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
        'abbreviation' => 'required|string',
        'voting_date_id'=> 'required|exists:voting_dates,id',
        ]);

        $region = Region::create([
            'name' => $request->name,
            'abbreviation' => $request->abbreviation,
            'voting_date_id' => $request->voting_date_id,
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

        $region->delete();

        return response()->json(['message' => 'Region and all related records deleted successfully.']);
    }

}
