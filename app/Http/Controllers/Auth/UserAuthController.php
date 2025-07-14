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

    public function showLoginForm()
    {
        return view('auth.login');
    }

    
     public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
            'user_type' => 'required|in:user,provider'
        ]);

        $credentials = [
            'phone' => $request->phone,
            'password' => $request->password,
            'activate' => 1
        ];

        if ($request->user_type === 'user') {
            // Use 'web' guard for users (this is the default Laravel guard)
            if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('user.dashboard'));
            }
        } elseif ($request->user_type === 'provider') {
            // Use 'provider' guard for providers
            if (Auth::guard('provider')->attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('provider.dashboard'));
            }
        }

        return back()->withErrors([
            'phone' => __('messages.invalid_credentials'),
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        // Check which guard is currently authenticated and logout accordingly
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        } elseif (Auth::guard('provider')->check()) {
            Auth::guard('provider')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
   

 
}