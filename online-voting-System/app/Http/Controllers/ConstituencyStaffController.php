<?php

namespace App\Http\Controllers;

use App\Models\Constituency;
use Illuminate\Http\Request;

class ConstituencyStaffController extends Controller
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
        'constituencies_id' => 'required|exists:constituencies,id',
        'first_name' => 'required|string',
        'middle_name' => 'required|string',
        'last_name' => 'required|string',
        'gender' => 'required|in:male,female',
        ]);

        $constituencystaff = Constituency::create([
            'user_id' => $request->user_id,
            'constituencies_id' => $request->constituencies_id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
        ]);

         return response()->json([
            'message' => 'constituency staff created successfully',
            'data' => $constituencystaff,
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
