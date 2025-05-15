<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\InternRegisterRequest;
use App\Models\Intern;
use Illuminate\Support\Facades\Log;

class InternAuthController extends Controller
{
    public function showLoginForm()
    {
        try {
            return view('intern.auth.login');
        } catch (\Exception $e) {
            Log::error('Error showing login form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading login page. Please try again.');
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            if (intern_guard()->attempt($credentials)) {
                return redirect()->route('intern.dashboard');
            }
            return back()->withErrors(['email' => 'Invalid credentials']);
        } catch (\Exception $e) {
            Log::error('Error during login: ' . $e->getMessage());
            return back()->withErrors(['email' => 'An error occurred during login. Please try again.']);
        }
    }

    public function showRegisterForm()
    {
        try {
            return view('intern.auth.register');
        } catch (\Exception $e) {
            Log::error('Error showing register form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading registration page. Please try again.');
        }
    }

    public function register(InternRegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $intern = Intern::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
            ]);

            intern_guard()->login($intern);
            return redirect()->route('intern.dashboard');
        } catch (\Exception $e) {
            Log::error('Error during registration: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred during registration. Please try again.'])->withInput();
        }
    }

    public function logout()
    {
        try {
            intern_guard()->logout();
            return redirect()->route('intern.login');
        } catch (\Exception $e) {
            Log::error('Error during logout: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error during logout. Please try again.');
        }
    }
}
