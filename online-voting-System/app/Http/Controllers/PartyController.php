<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;

class PartyController extends Controller
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
            'name' => 'required|string',
            'abbrevation' => 'required|string',
            'leader' => 'required|string',
            'foundation_year' => 'required|date',
            'participation_area' => 'required|in:national,regional',
            'region_id' => 'string|exists:regions,id',
            'status' => 'in:active,inactive',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('parties', 'public');
    }

        $regionId = null;

        if ($request->participation_area === 'regional') {
            $regionId = $request->region_id;
        }

        $party = Party::create([
            'name' => $request->name,
            'abbrevation' => $request->abbrevation,
            'leader' => $request->leader,
            'foundation_year' => $request->foundation_year,
            'participation_area' => $request->participation_area,
            'region_id' => $regionId,
            'status' => $request->status ?? 'active',
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Party registered successfully',
            'party' => $party,
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
        $party = Party::find($id);

        if (!$party) {
            return response()->json(['message' => 'Party not found.'], 404);
        }

        $party->update($request->all());

        return response()->json([
            'message' => 'party information updated successfully',
            'data' => $party,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $party = Party::find($id);

        if (!$party) {
            return response()->json(['message' => 'Party not found.'], 404);
        }

        // Delete related candidates
        $party->candidates()->delete();

        // Delete the party
        $party->delete();

        return response()->json(['message' => 'Party and related candidates deleted successfully.']);
    }

    /**
     * change parties status
     */

     public function partyStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $party = Party::find($id);

        if (!$party) {
            return response()->json(['message' => 'Party not found.'], 404);
        }

        $party->status = $request->status;
        $party->save();

        // Update all related candidates
        $party->candidates()->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Party and related candidates status updated successfully.',
            'party' => $party
        ]);
    }
}
