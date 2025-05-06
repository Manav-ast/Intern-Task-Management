<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminRegisterRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        try {
            return view('admin.auth.login');
        } catch (\Exception $e) {
            Log::error('Error showing login form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading login page. Please try again.');
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            if (Auth::guard('admin')->attempt($credentials)) {
                return redirect()->route('admin.dashboard');
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
            return view('admin.auth.register');
        } catch (\Exception $e) {
            Log::error('Error showing register form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading registration page. Please try again.');
        }
    }

    public function register(AdminRegisterRequest $request)
    {
        try {
            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            auth('admin')->login($admin);
            return redirect()->route('admin.dashboard');
        } catch (\Exception $e) {
            Log::error('Error during registration: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred during registration. Please try again.'])->withInput();
        }
    }

    public function logout()
    {
        try {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login');
        } catch (\Exception $e) {
            Log::error('Error during logout: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error during logout. Please try again.');
        }
    }
}
