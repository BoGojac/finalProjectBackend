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
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string',
            'role' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'status' => 'in:active,inactive',
        ]);

        $user = User::create([
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

    public function getUser(Request $request){

        $user = User::all();

        return response()->json([
            'message' => 'lists of registered users',
            'data' => $user,
        ], 201);
    }

    public function login(Request $request){
        $credentials = $request -> validate([
            'email' => 'required|email',
            'password' =>'required',
        ]);
        if (! Auth::attempt($credentials)) {
        return response()->json([
            'message' => 'User not found'
        ], 401);
        }
        /**@var User $user */
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message'       => 'Login success',
            'access_token'  => $token,
        ]);

    }

    public function logout(Request $request){
         /** @var User $user */
            $user = $request->user();

            // Use delete on the token relationship
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json([
                 'message' => 'Logout success',
            ]);
    }
}
