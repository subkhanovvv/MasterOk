<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Setting;
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
        $settings = Setting::find(1);

        return view('auth.login', compact('settings'));
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
        $settings = Setting::find(1);


        return view('pages.profile.profile', compact('user', 'settings'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'name'  => 'nullable|string|max:255',
            'password' => 'nullable|min:8',
        ]);
        $data = [];

        if ($request->filled('name') && $request->name !== auth()->user()->name) {
            $data['name'] = $request->name;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        if (empty($data)) {
            return back()->with('error', 'no changes were made');
        }
        User::find(auth()->id())->update($data);

        return back()->with('success', 'Профиль обновлен!');
    }
}
