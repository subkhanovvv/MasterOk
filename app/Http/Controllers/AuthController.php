<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function logout() {
       Auth::logout();
    
        return redirect('login');
    }
    
}
