<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Use 'web' guard for users
        if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {

            // Users don't need specific role, proceed to dashboard
            $request->session()->regenerate();
            return redirect(route('user.dashboard'));
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

        // Use 'web' guard for providers
        if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();

            // Check if user has provider role
            if (!$user->hasRole('provider')) {
                Auth::logout();
                return back()->withErrors([
                    'email' => __('messages.invalid_credentials'),
                ])->withInput($request->except('password'));
            }

            $request->session()->regenerate();
            return redirect(route('provider.dashboard'));
        }

        return back()->withErrors([
            'email' => __('messages.invalid_credentials'),
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        // Determine redirect based on user role
        if ($user && $user->hasRole('admin')) {
            $redirectRoute = 'admin.showlogin';
        } elseif ($user && $user->hasRole('provider')) {
            $redirectRoute = 'provider.login';
        } else {
            $redirectRoute = 'user.login';
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($redirectRoute);
    }
}
