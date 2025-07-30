<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
  public function showLoginForm()
  {
    if (Auth::guard('admin')->check()) {
      return redirect()->route('admin.dashboard');
    }
    return view('admin.auth.login');
  }

  public function login(Request $request)
  {
    $credentials = $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    // Attempt login with remember me option
    if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
      $request->session()->regenerate();

      // Redirect to intended URL if set, otherwise to dashboard
      return redirect()->intended(route('admin.dashboard'));
    }

    return back()->withInput($request->only('email', 'remember'))->withErrors([
      'email' => 'The provided credentials do not match our records.',
    ]);
  }

  public function logout(Request $request)
  {
    Auth::guard('admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('admin.login');
  }

  public function showRegisterForm()
  {
    return view('admin.auth.register');
  }

  public function register(Request $request)
  {
    // Validate
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:admins',
      'password' => 'required|string|min:8|confirmed',
      'role' => 'required|in:admin,super_admin',
    ]);

    // Create admin
    Admin::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role' => $request->role,
    ]);

    return redirect()->route('admin.admins')
      ->with('success', 'Admin created successfully');
  }
}
