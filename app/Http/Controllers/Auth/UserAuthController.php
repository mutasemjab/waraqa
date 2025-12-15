<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{

    public function showUserLoginForm()
    {
        return view('auth.user.login');
    }

    public function showProviderLoginForm()
    {
        return view('auth.provider.login');
    }


     public function loginUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'activate' => 1
        ];

        // Use 'web' guard for users (this is the default Laravel guard)
        if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('user.dashboard'));
        }

        return back()->withErrors([
            'email' => __('messages.invalid_credentials'),
        ])->withInput($request->except('password'));
    }

    public function loginProvider(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'activate' => 1
        ];

        // Use 'provider' guard for providers
        if (Auth::guard('provider')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('provider.dashboard'));
        }

        return back()->withErrors([
            'email' => __('messages.invalid_credentials'),
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        // Check which guard is currently authenticated and logout accordingly
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            $redirectRoute = 'user.login';
        } elseif (Auth::guard('provider')->check()) {
            Auth::guard('provider')->logout();
            $redirectRoute = 'provider.login';
        } else {
            $redirectRoute = 'user.login';
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($redirectRoute);
    }
   

 
}