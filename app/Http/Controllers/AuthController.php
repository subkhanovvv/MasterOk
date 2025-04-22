<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }
    public function register()
    {
        return view('auth.register');
    }
    public function ProcessLogin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt(['name' => $validated['name'], 'password' => $validated['password']], $request->remember)) {
            return redirect()->intended('index');
        }

        return back()->withErrors(['name' => 'Invalid credentials'])->withInput($request->only('name'));
    }
    public function logout()
    {
        Auth::logout();

        return redirect('login');
    }


    public function profile()
    {
        $user = Auth::user();

        return view('pages.profile.profile', compact('user'));
    }
    public function edit_profile($id)
    {
        $user = User::find($id);
        return view('pages.profile.edit-profile', compact('user'));
    }

    public function update_profile(Request $request)
    {
        return back()->with('success', 'Профиль обновлен!');
    }
}
