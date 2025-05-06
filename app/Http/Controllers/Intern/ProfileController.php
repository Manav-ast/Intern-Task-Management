<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('intern.profile');
    }

    public function update(Request $request)
    {
        $intern = auth('intern')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('interns')->ignore($intern->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

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
