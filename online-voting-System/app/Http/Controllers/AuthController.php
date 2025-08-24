<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

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



    public function getUser(Request $request)
    {
        $perPage = $request->input('per_page', 10); // default to 10
        $users = User::with([
            'admin:id,user_id,first_name,middle_name,last_name,gender',
            'board_manager:id,user_id,first_name,middle_name,last_name,gender',
            'constituency_staff:id,user_id,first_name,middle_name,last_name,gender',
            'polling_station_staff:id,user_id,first_name,middle_name,last_name,gender',
            'candidate:id,user_id,first_name,middle_name,last_name,gender',
            'voter:id,user_id,first_name,middle_name,last_name,gender',
        ])->orderByDesc('created_at')->paginate($perPage);

        // Map results while keeping pagination
        $users->getCollection()->transform(function ($user) {
            $detail = $user->admin
                ?: $user->board_manager
                ?: $user->constituency_staff
                ?: $user->polling_station_staff
                ?: $user->candidate
                ?: $user->voter;

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

        return response()->json($users);
    }


    public function login(Request $request){
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid Credential'
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        // Status check before issuing token
        if ($user->status !== 'active') {
            $role = strtolower(str_replace(' ', '', $user->role));

            $message = match ($role) {
                'voter' => 'Sorry, you can\'t login now. You have been deactivated. Please contact your polling station or higher officials.',
                'candidate' => 'Sorry, you can\'t login now. You have been deactivated. Please contact your Constituency or higher officials.',
                'admin', 'boardmanager', 'constituencystaff', 'pollingstationstaff' =>
                    'Sorry, you can\'t login now. You have been deactivated. Please contact the System Admin.',
                default => 'Account deactivated. Please contact support.'
            };

            return response()->json([
                'message' => $message
            ], 403); // Use 403 Forbidden
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'user' => $user,
            'access_token' => $token,
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

    public function show(Request $request, $id){

        $user = User::find($id);

          if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'message' => 'here is specific user',
            'user' => $user
        ]);
    }

    public function get_admin(Request $request, $id){

        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }

        $admin = $user->admin;

        if(!$admin){
            return response()->json(['message' => 'Admin not found'], 404);
        }

        return response()->json([
            'message' => 'here is specific admin user',
            'data' => $admin
        ]);
    }

    public function get_boardmanager(Request $request, $id){

        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }

        $boardmanager = $user->board_manager;

        if(!$boardmanager){
            return response()->json(['message' => 'BoardManager not found'], 404);
        }

        return response()->json([
            'message' => 'here is specific boardmanager user',
            'data' => $boardmanager
        ]);
    }

    public function get_constituencystaff(Request $request, $id){

        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }

          $constituencystaff = $user->constituency_staff;

        if(!$constituencystaff){
            return response()->json(['message' => 'Constituency Staff not found'], 404);
        }

        return response()->json([
            'message' => 'here is specific constituencystaff user',
            'data' => $constituencystaff
        ]);
    }

    public function get_pollingstationstaff(Request $request, $id){

        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }

        $pollingstationstaff = $user->polling_station_staff;

        if(!$pollingstationstaff){
            return response()->json(['message' => 'pollingstationstaff not found'], 404);
        }

        return response()->json([
            'message' => 'here is specific pollingstationstaff user',
            'data' => $pollingstationstaff
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
            'role' => 'required|string|in:Admin,Board Manager,Constituency Staff,Polling Station Staff,Voter,Candidate',
        ]);

        $oldRole = $user->role;
        $newRole = $request->role;

        //  Prevent role change if user is Voter or Candidate
        if (in_array($oldRole, ['Voter', 'Candidate']) && $oldRole !== $newRole) {
            return response()->json([
                'message' => "Users with role {$oldRole} cannot change their role."
            ], 403);
        }

        // Update user table first
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->username = $request->username;

        // Update role only if allowed
        if ($oldRole !== $newRole) {
            $user->role = $newRole;
        }
        $user->save();

        // Common profile data
        $profileData = [
            'first_name'   => $request->first_name,
            'middle_name'  => $request->middle_name,
            'last_name'    => $request->last_name,
            'gender'       => $request->gender,
        ];

        // Delete from old role table if role changed
        if ($oldRole !== $newRole) {
            match ($oldRole) {
                'Admin' => $user->admin()->delete(),
                'Board Manager' => $user->board_manager()->delete(),
                'Constituency Staff' => $user->constituency_staff()->delete(),
                'Polling Station Staff' => $user->polling_station_staff()->delete(),
                'Voter' => $user->voter()->delete(),
                'Candidate' => $user->candidate()->delete(),
                default => null,
            };
        }

        // Insert/update in new role table
        match ($newRole) {
            'Admin' => $user->admin()->updateOrCreate(['user_id' => $user->id], $profileData),

            'Board Manager' => $user->board_manager()->updateOrCreate(['user_id' => $user->id], $profileData),

            'Constituency Staff' => $user->constituency_staff()->updateOrCreate(
                ['user_id' => $user->id],
                array_merge($profileData, [
                    'constituency_id' => $request->constituency_id,
                ])
            ),

            'Polling Station Staff' => $user->polling_station_staff()->updateOrCreate(
                ['user_id' => $user->id],
                array_merge($profileData, [
                    'polling_station_id' => $request->polling_station_id,
                ])
            ),

            'Voter' => $user->voter()->updateOrCreate(['user_id' => $user->id], $profileData),

            'Candidate' => $user->candidate()->updateOrCreate(['user_id' => $user->id], $profileData),

            default => null,
        };

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->fresh()->load([
                'admin',
                'board_manager',
                'constituency_staff',
                'polling_station_staff',
                'candidate',
                'voter'
            ]),
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


    public function updatePassword(Request $request,string $id)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', 'min:8'],
        ]);
         /** @var \App\Models\User $user */
           $user = User::find($id);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'The current password is incorrect.'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Password updated successfully.']);
    }

}
