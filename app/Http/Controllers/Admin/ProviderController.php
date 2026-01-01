<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Country;
use App\Models\Provider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProviderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:provider-table')->only(['index']);
        $this->middleware('permission:provider-add')->only(['create', 'store']);
        $this->middleware('permission:provider-edit')->only(['edit', 'update']);
        $this->middleware('permission:provider-delete')->only(['destroy']);
    }

   public function index()
    {
        $providers = Provider::all();
        return view('admin.providers.index', compact('providers'));
    }


    public function create()
    {
        $countries= Country::get();
        return view('admin.providers.create',compact('countries'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users',
            'email' => 'nullable|email|unique:users',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'password' => 'required|string|min:6',
            'activate' => 'nullable|in:1,2',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('providers.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Prepare user data
        $userData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'activate' => $request->activate ?? 1,
            'country_id' => $request->country_id,
        ];

        // Handle photo upload
        if ($request->has('photo')) {
            $the_file_path = uploadImage('assets/admin/uploads', $request->photo);
            $userData['photo'] = $the_file_path;
        }

        // Create user first
        $user = User::create($userData);

        // Create provider linked to user
        Provider::create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        return redirect()
            ->route('providers.index')
            ->with('success', 'Provider created successfully');
    }


    public function show($id)
    {
        $provider = Provider::with('user')->findOrFail($id);

        return view('admin.providers.show', compact('provider'));
    }


    public function edit($id)
    {
        $provider = Provider::with('user')->findOrFail($id);
        $countries = Country::get();
        return view('admin.providers.edit', compact('provider', 'countries'));
    }


    public function update(Request $request, $id)
    {
        $provider = Provider::with('user')->findOrFail($id);
        $user = $provider->user;

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'password' => 'nullable|string|min:6',
            'activate' => 'nullable|in:1,2',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('providers.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        // Prepare user data to update
        $userData = [];
        if ($request->filled('name')) {
            $userData['name'] = $request->name;
        }
        if ($request->filled('phone')) {
            $userData['phone'] = $request->phone;
        }
        if ($request->filled('email')) {
            $userData['email'] = $request->email;
        }
        if ($request->filled('activate')) {
            $userData['activate'] = $request->activate;
        }
        if ($request->filled('country_id')) {
            $userData['country_id'] = $request->country_id;
        }

        // Handle photo upload
        if ($request->has('photo')) {
            $the_file_path = uploadImage('assets/admin/uploads', $request->photo);
            $userData['photo'] = $the_file_path;
        }

        // Handle password
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Update user
        if (!empty($userData)) {
            $user->update($userData);
        }

        return redirect()
            ->route('providers.index')
            ->with('success', 'provider updated successfully');
    }


    public function destroy($id)
    {
        $provider = Provider::findOrFail($id);
        
        
        $provider->delete();

        return redirect()
            ->route('providers.index')
            ->with('success', 'provider deleted successfully');
    }

}