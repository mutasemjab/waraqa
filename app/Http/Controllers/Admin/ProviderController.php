<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Driver;
use App\Models\Option;
use App\Models\Provider;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProviderController extends Controller
{
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
            'phone' => 'required|string|unique:providers',
            'email' => 'nullable|email|unique:providers',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'fcm_token' => 'nullable|string',
            'password' => 'required',
            'activate' => 'nullable|in:1,2',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('providers.create')
                ->withErrors($validator)
                ->withInput();
        }

        $userData = $request->except('photo');
    

        // Handle photo upload
        if ($request->has('photo')) {
                $the_file_path = uploadImage('assets/admin/uploads', $request->photo);
                 $userData['photo'] = $the_file_path;
             }
                
        $providerData['password'] = Hash::make($request->password);

        Provider::create($userData);

        return redirect()
            ->route('providers.index')
            ->with('success', 'Provider created successfully');
    }


    public function show($id)
    {
        $provider = Provider::findOrFail($id);
        
        return view('admin.providers.show', compact('provider'));
    }


    public function edit($id)
    {
        $provider = Provider::findOrFail($id);
          $countries= Country::get();
        return view('admin.providers.edit', compact('provider','countries'));
    }


    public function update(Request $request, $id)
    {
        $provider = Provider::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|unique:providers,phone,' . $id,
            'email' => 'nullable|email|unique:providers,email,' . $id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'fcm_token' => 'nullable|string',
            'password' => 'nullable',
            'activate' => 'nullable|in:1,2',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('providers.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $providerData = $request->except('photo','password');

        // Handle photo upload
          if ($request->has('photo')) {
                $the_file_path = uploadImage('assets/admin/uploads', $request->photo);
                $providerData['photo'] = $the_file_path;
             }
          if ($request->has('password')) {
                $providerData['password'] = Hash::make($request->password);
             }

        $provider->update($providerData);

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