<?php

namespace App\Http\Controllers;

use App\Models\ConstituencyStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConstituencyStaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $constituencystaff = ConstituencyStaff::all();
        return response()->json([
            'message' => 'here is the constituencies',
            'data' => $constituencystaff
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $request->validate([
        'user_id' => 'required|exists:Users,id',
        'constituency_id' => 'required|exists:constituencies,id',
        'first_name' => 'required|string',
        'middle_name' => 'required|string',
        'last_name' => 'required|string',
        'gender' =>  'required|in:Male,Female',
        ]);

        $constituencystaff = ConstituencyStaff::create([
            'user_id' => $request->user_id,
            'constituency_id' => $request->constituency_id,
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
        $staff = ConstituencyStaff::find($id);
        return response()->json([
            'message' =>  'this is the constituency staff you search for',
            'data' => $staff,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $staff = ConstituencyStaff::where('user_id', $id)->first();

        if (!$staff) {
            return response()->json(['message' => 'Constituency Staff not found.'], 404);
        }

        $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'gender' => 'required|in:Male,Female',
            'constituency_id' => 'required|exists:constituencies,id',
        ]);

        $staff->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'constituency_id' => $request->constituency_id,
        ]);

        return response()->json([
            'message' => 'Constituency staff updated successfully',
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

    public function get_Auth_ConstituencyStaff()
    {
        $user = Auth::user(); // get base user
        $constituencyStaff = $user->constituency_staffs;

        return response()->json([
            'message' => 'This is the constituency staff logged in',
            'data' => [
                'user' => $user,
                'constituency_staff' => $constituencyStaff,
            ],
        ]);
    }

}
