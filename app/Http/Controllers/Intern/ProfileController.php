<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('intern.profile');
    }

    public function update(ProfileUpdateRequest $request)
    {
        $intern = intern();
        $validated = $request->validated();

        $intern->name = $validated['name'];
        $intern->email = $validated['email'];

        if (!empty($validated['password'])) {
            $intern->password = Hash::make($validated['password']);
        }

        $intern->save();

        return redirect()->route('intern.dashboard')->with('success', [
            'title' => 'Profile Updated!',
            'message' => 'Your profile has been updated successfully.'
        ]);
    }
}
