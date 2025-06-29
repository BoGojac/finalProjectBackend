<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'voting_date_id'=> 'required|exists:voting_dates,id',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string',
            'role' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'status' => 'in:active,inactive',
        ]);

        $user = User::create([
            'voting_date_id' => $request->voting_date_id,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'phone_number' => $request->phone_number,
            'role' => $request->role,
            'email' => $request->email,
            'status'=> $request->status ?? 'active',
        ]);

        return response()->json([
            'message' => 'user registered successfully',
            'user' => $user,
        ], 201);
    }

    public function getUser()
    {
        $users = User::with([
            'admin:id,user_id,first_name,middle_name,last_name,gender',
            'board_managers:id,user_id,first_name,middle_name,last_name,gender',
            'constituency_staffs:id,user_id,first_name,middle_name,last_name,gender',
            'polling_station_staffs:id,user_id,first_name,middle_name,last_name,gender',
            'candidates:id,user_id,first_name,middle_name,last_name,gender',
            'voters:id,user_id,first_name,middle_name,last_name,gender',
        ])->get()->map(function ($user) {
            $detail = $user->admin
                ?: $user->board_managers
                ?: $user->constituency_staffs
                ?: $user->polling_station_staffs
                ?: $user->candidates
                ?: $user->voters;

            return [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'role' => $user->role,
                'status' => $user->status,
                'first_name' => $detail->first_name ?? null,
                'middle_name' => $detail->middle_name ?? null,
                'last_name' => $detail->last_name ?? null,
                'gender' => $detail->gender ?? null,
            ];
        });

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
        ]);
    }


    public function login(Request $request){
        $credentials = $request -> validate([
            'email' => 'required|email',
            'password' =>'required',
        ]);
        if (! Auth::attempt($credentials)) {
        return response()->json([
            'message' => 'Invalid Credential'
        ], 401);
        }
        /**@var User $user */
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message'       => 'Login success',
             'user' => $user,
            'access_token'  => $token,
        ]);

    }

    public function logout(Request $request)
    {
        /** @var User|null $user */
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $user->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json([
            'message' => 'Logout success',
        ]);
    }


    /**
     * User Updating
     */


     public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'required|string',
            'username' => 'required|string|unique:users,username,' . $id,
        ]);

        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->username = $request->username;
        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }




    /**
     * Toggle user status between active and inactive.
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $user->status = $request->status;
        $user->save();

        return response()->json([
            'message' => 'User status updated successfully.',
            'user' => $user
        ]);
    }

}
