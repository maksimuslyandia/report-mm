<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Http\RedirectResponse;
//use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Session;


class CustomAuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function registration()
    {
        return view('auth.registration');
    }

    public function customRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function customLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['login_error' => 'Invalid email or password']);
    }

    public function dashboard()
    {
        if(Auth::check()){
            return view('dashboard');
        }
        return redirect()->route('login')->withErrors('You are not allowed to access');
    }

    public function signOut()
    {
        Auth::logout();
        return redirect()->route('login');
    }

}
