<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $candidates = Candidate::with('user', 'party')->get();
        return response()->json($candidates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateCandidateRequest($request);

        // Age validation: ensure candidate is at least 21 years old
        if (Carbon::parse($validated['birth_date'])->age < 21) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => ['birth_date' => ['Candidate must be at least 21 years old']]
            ], 422);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $validated['original_image_name'] = $image->getClientOriginalName();
            $validated['image'] = $image->store('candidates', 'public');
        }

        $validated['duration_of_residence'] = $validated['residence_duration'] . ' ' . $validated['residence_unit'];
        $validated['registration_date'] = now()->format('m-d-Y');
         

        $candidate = Candidate::create($validated);

        return response()->json([
            'message' => 'Candidate registered successfully',
            'data' => $candidate,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $candidate = Candidate::with('user')->find($id);

        if (!$candidate) {
            return response()->json(['message' => 'Candidate not found.'], 404);
        }

        return response()->json([
            'message' => 'here is the candidate you search for',
            'data'=> $candidate]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $candidate = Candidate::find($id);

        if (!$candidate) {
            return response()->json(['message' => 'Candidate not found.'], 404);
        }

        $validated = $this->validateCandidateRequest($request);

        if (Carbon::parse($validated['birth_date'])->age < 21) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => ['birth_date' => ['Candidate must be at least 21 years old']]
            ], 422);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $validated['original_image_name'] = $image->getClientOriginalName();
            $validated['image'] = $image->store('candidates', 'public');
        }

        $validated['duration_of_residence'] = $validated['residence_duration'] . ' ' . $validated['residence_unit'];

        $candidate->update($validated);

        return response()->json([
            'message' => 'Candidate information updated successfully',
            'data' => $candidate,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $candidate = Candidate::find($id);

        if (!$candidate) {
            return response()->json(['message' => 'Candidate not found.'], 404);
        }

        $candidate->delete();

        return response()->json(['message' => 'Candidate deleted successfully.']);
    }

    /**
     * get authenticated candidate
     */
    public function get_Auth_Candidate()
    {
        $user = Auth::user();
        $candidate = $user->candidate;
        return response()->json([
            'message'=> 'this the candidate staff loged in',
            'data'=> [
                'user' => $user,
                'candidate' => $candidate ,
            ],
        ]);
    }


    /**
     * Common validation logic.
     */
    protected function validateCandidateRequest(Request $request): array
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'party_id' => 'nullable|required_if:candidate_type,party|exists:parties,id',
            'constituency_id' => 'required|exists:constituencies,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'birth_date' => 'required|date',
            'disability' => 'required|in:None,Visual,Hearing,Physical,Intellectual,Other',
            'disability_type' => 'nullable|required_if:disability,Other|string|max:255',
            'residence_duration' => 'required|numeric|min:0',
            'residence_unit' => 'required|in:months,years',
            'home_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'candidate_type' => 'required|in:individual,party',
        ]);

        // Custom duration rules
        if ($validated['residence_unit'] === 'months' && $validated['residence_duration'] < 6) {
            abort(response()->json([
                'message' => 'Validation error',
                'errors' => ['residence_duration' => ['Must be at least 6 months']]
            ], 422));
        }

        if ($validated['residence_unit'] === 'years' && $validated['residence_duration'] < 1) {
            abort(response()->json([
                'message' => 'Validation error',
                'errors' => ['residence_duration' => ['Must be at least 1 year']]
            ], 422));
        }

        return $validated;
    }
}
