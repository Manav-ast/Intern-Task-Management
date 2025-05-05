<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\InternRegisterRequest;
use App\Models\Intern;

class InternAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('intern.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::guard('intern')->attempt($credentials)) {
            return redirect()->route('intern.dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function showRegisterForm()
    {
        return view('intern.auth.register');
    }

    public function register(InternRegisterRequest $request)
    {
        $intern = Intern::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        auth('intern')->login($intern);
        return redirect()->route('intern.dashboard');
    }

    public function logout()
    {
        Auth::guard('intern')->logout();
        return redirect()->route('intern.login');
    }
}
