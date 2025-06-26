<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parties = Party::with('region:id,name')->get()->map(function ($party) {
            return [
                'id' => $party->id,
                'name' => $party->name,
                'abbrevation' => $party->abbrevation,
                'leader' => $party->leader,
                'foundation_year' => $party->foundation_year,
                'headquarters' => $party->headquarters,
                'participation_area' => $party->participation_area,
                'region_id' => $party->region_id,
                'region_name' => optional($party->region)->name,
                'status' => $party->status,
                'original_image_name' => $party->original_image_name, // ✅ FIXED
            ];
        });

        return response()->json([
            'message' => 'here is the Parties',
            'data' => $parties
        ]);
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
            'headquarters' => 'required|string', // ✅ Add this
            'participation_area' => 'required|in:national,regional',
            'region_id' => 'string|exists:regions,id',
            'voting_date_id'=> 'required|exists:voting_dates,id',
            'status' => 'in:active,inactive',
            'image' => 'nullable|image|max:2048',
        ]);

        // ✅ Get original image name and save image
        $originalName = null;
        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $originalName = $image->getClientOriginalName();
            $imagePath = $image->store('parties', 'public');
        }

        $regionId = null;

        if ($request->participation_area === 'regional') {
            $regionId = $request->region_id;
        }

        $party = Party::create([
            'voting_date_id' => $request->voting_date_id,
            'name' => $request->name,
            'abbrevation' => $request->abbrevation,
            'leader' => $request->leader,
            'foundation_year' => $request->foundation_year,
            'headquarters' => $request->headquarters, // ✅ Now included
            'participation_area' => $request->participation_area,
            'region_id' => $regionId,
            'status' => $request->status ?? 'active',
            'image' => $imagePath,
            'original_image_name' => $originalName, // ✅ Save real file name
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

        $validated = $request->validate([
                'name' => 'required|string',
                'abbrivation' => 'required|string',
                'leader' => 'required|string',
                'foundation_year' => 'required|date',
                'headquarters' => 'required|string',
                'participation_area' => 'required|in:national,regional',
                'region_id' => 'nullable|string|exists:regions,id',
                'image' => 'nullable|image|max:2048',
            ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $validated['image'] = $image->store('parties', 'public');
            $validated['original_image_name'] = $image->getClientOriginalName();
        }

        if ($validated['participation_area'] !== 'regional') {
            $validated['region_id'] = null;
        }

        $party->update($validated);

        return response()->json([
            'message' => 'Party information updated successfully',
            'data' => $party,
        ]);
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

        // ✅ Delete related candidates
        $party->candidates()->delete();

        // ✅ Delete image file if it exists
        if ($party->image && Storage::disk('public')->exists($party->image)) {
            Storage::disk('public')->delete($party->image);
        }

        // ✅ Delete the party
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
