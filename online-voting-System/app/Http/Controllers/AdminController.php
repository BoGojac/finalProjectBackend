<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admin = Admin::all();
        return response()->json([
            'message' => 'here is information data of an admin',
            'data' => $admin,
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'user_id' => 'required|exists:Users,id',
        'first_name' => 'required|string',
        'middle_name' => 'required|string',
        'last_name' => 'required|string',
        'gender' => 'required|in:male,female',
        ]);

        $admin = Admin::create([
            'user_id' => $request->user_id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
        ]);

         return response()->json([
            'message' => 'Admin created successfully',
            'data' => $admin,
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
        $admin = Admin::where('user_id', $id)->first();

        if (!$admin) {
            return response()->json(['message' => 'Admin not found.'], 404);
        }

        $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'gender' => 'required|in:male,female',
        ]);

        $admin->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
        ]);

        return response()->json([
            'message' => 'Admin updated successfully',
            'data' => $admin,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['message' => 'Admin with this id not found.'], 404);
        }

        $admin->delete();

        return response()->json(['message' => 'deleted successfully.']);
    }
}
