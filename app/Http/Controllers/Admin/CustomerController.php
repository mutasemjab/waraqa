<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:customer-table')->only(['index']);
        $this->middleware('permission:customer-add')->only(['create', 'store']);
        $this->middleware('permission:customer-edit')->only(['edit', 'update']);
        $this->middleware('permission:customer-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = User::role('customer')->get();
        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::get();
        return view('admin.customers.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users',
            'password' => 'required',
            'email' => 'nullable|email|unique:users',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'fcm_token' => 'nullable|string',
            'activate' => 'nullable|in:1,2',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('customers.create')
                ->withErrors($validator)
                ->withInput();
        }

        $userData = $request->except('photo');

        // Handle photo upload
        if ($request->has('photo')) {
            $the_file_path = uploadImage('assets/admin/uploads', $request->photo);
            $userData['photo'] = $the_file_path;
        }
        $userData['password'] = bcrypt($userData['password']);

        $customer = User::create($userData);

        // Assign customer role
        $customer->assignRole('customer');

        return redirect()
            ->route('customers.index')
            ->with('success', __('messages.customer_created_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = User::role('customer')->findOrFail($id);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = User::role('customer')->findOrFail($id);
        $countries = Country::get();
        return view('admin.customers.edit', compact('customer', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer = User::role('customer')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'password' => 'nullable',
            'phone' => 'sometimes|string|unique:users,phone,' . $id,
            'email' => 'nullable|email|unique:users,email,' . $id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'fcm_token' => 'nullable|string',
            'activate' => 'nullable|in:1,2',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('customers.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $userData = $request->except('photo', 'password');

        // Handle photo upload
        if ($request->has('photo')) {
            $the_file_path = uploadImage('assets/admin/uploads', $request->photo);
            $userData['photo'] = $the_file_path;
        }

        if ($request->has('password') && $request->password) {
            $userData['password'] = Hash::make($request->password);
        }

        $customer->update($userData);

        return redirect()
            ->route('customers.index')
            ->with('success', __('messages.customer_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = User::role('customer')->findOrFail($id);

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', __('messages.customer_deleted_successfully'));
    }

    /**
     * Search for customers (API endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $term = $request->get('term', '');
        $limit = $request->get('limit', 5);

        $customers = User::role('customer')
            ->where('activate', 1)
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            })
            ->limit($limit)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'text' => $customer->name
                ];
            });

        return response()->json($customers);
    }
}
