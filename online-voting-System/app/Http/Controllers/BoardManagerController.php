<?php

namespace App\Http\Controllers;

use App\Models\BoardManager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $boardmanager = BoardManager::all();
        return response()->json([
            'message' => 'information of board managers',
            'data' => $boardmanager,
        ]);
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
            'gender' => 'required|in:Male,Female',
        ]);


        $boardmanager = BoardManager::create([
            'user_id' => $request->user_id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
        ]);

         return response()->json([
            'message' => 'boardmanager created successfully',
            'boardmanager' => $boardmanager,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       $boardmanager = BoardManager::find($id);
        return response()->json([
            'message' =>  'this is the constituency staff you search for',
            'data' => $boardmanager,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find by user_id
        $boardmanager = BoardManager::where('user_id', $id)->first();

        if (!$boardmanager) {
            return response()->json(['message' => 'Boardmanager not found.'], 404);
        }

        $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'gender' =>  'required|in:Male,Female',
        ]);

        $boardmanager->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
        ]);

        return response()->json([
            'message' => 'Boardmanager information updated successfully',
            'data' => $boardmanager,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $boardmanager = BoardManager::find($id);

        if (!$boardmanager) {
            return response()->json(['message' => 'BoardManager not found.'], 404);
        }

        // Delete the party
        $boardmanager->delete();

        return response()->json(['message' => 'Boardmanager deleted successfully.']);
    }

    /**
     * get authenticated board manager
     */
    public function get_Auth_BoardManager()
    {
        $user = Auth::user();
        $board_manager = $user->board_manager;
        return response()->json([
            'message'=> 'this the boardmanager staff loged in',
            'data'=> [
                'user' => $user,
                'board_manager' => $board_manager,
            ],
        ]);
    }
}
