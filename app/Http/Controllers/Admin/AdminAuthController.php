<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            if (admin_guard()->attempt($credentials)) {
                return redirect()->route('admin.dashboard');
            }
            return back()->withErrors(['email' => 'Invalid credentials']);
        } catch (\Exception $e) {
            Log::error('Error during login: ' . $e->getMessage());
            return back()->withErrors(['email' => 'An error occurred during login. Please try again.']);
        }
    }

    public function logout()
    {
        try {
            if (is_admin()) {
                admin_guard()->logout();
            }
            return redirect()->route('admin.login');
        } catch (\Exception $e) {
            Log::error('Error during logout: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error during logout. Please try again.');
        }
    }
}
