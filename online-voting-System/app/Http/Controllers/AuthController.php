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
            'username' => 'required|string',
            'password' => 'required|comfirmed|string',
            'phone_number' => 'string',
            'role' => 'required|string',
            'email' => "required|email",
        ]);
        
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'role' => $request->role,
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => 'user registered successfully',
            'user' => $user,
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
            'token_type'    => 'Bearer'
        ]);
        
    }

    public function logout(){

    }
}
