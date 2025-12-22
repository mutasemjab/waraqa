<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Event;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::get();
        return view('admin.users.create', compact('countries'));
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'fcm_token' => 'nullable|string',
            'activate' => 'nullable|in:1,2',
            'country_id' => 'nullable|exists:countries,id',
            'events' => 'nullable|array',
            'events.*.name' => 'required_with:events|string|max:255',
            'events.*.start_date' => 'required_with:events|date_format:Y-m-d\TH:i',
            'events.*.end_date' => 'required_with:events|date_format:Y-m-d\TH:i|after:events.*.start_date',
            'events.*.commission_percentage' => 'required_with:events|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('users.create')
                ->withErrors($validator)
                ->withInput();
        }

        $userData = $request->except('photo', 'events');

        // Handle photo upload
        if ($request->has('photo')) {
            $the_file_path = uploadImage('assets/admin/uploads', $request->photo);
            $userData['photo'] = $the_file_path;
        }
        $userData['password'] = bcrypt($userData['password']);

        $user = User::create($userData);

        // Create events if provided
        if ($request->has('events') && is_array($request->events)) {
            foreach ($request->events as $eventData) {
                Event::create([
                    'user_id' => $user->id,
                    'name' => $eventData['name'],
                    'start_date' => $eventData['start_date'],
                    'end_date' => $eventData['end_date'],
                    'commission_percentage' => $eventData['commission_percentage'],
                ]);
            }
        }

        DB::table('warehouses')->insert([
            'name' => 'مستودع ' . $request->name,
            'user_id' => $user->id,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::with('events')->findOrFail($id);
        $countries = Country::get();
        return view('admin.users.edit', compact('user', 'countries'));
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
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'password' => 'nullable',
            'phone' => 'sometimes|string|unique:users,phone,' . $id,
            'email' => 'nullable|email|unique:users,email,' . $id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'fcm_token' => 'nullable|string',
            'activate' => 'nullable|in:1,2',
            'country_id' => 'nullable|exists:countries,id',
            'events' => 'nullable|array',
            'events.*.name' => 'required_with:events|string|max:255',
            'events.*.start_date' => 'required_with:events|date_format:Y-m-d\TH:i',
            'events.*.end_date' => 'required_with:events|date_format:Y-m-d\TH:i|after:events.*.start_date',
            'events.*.commission_percentage' => 'required_with:events|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('users.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $userData = $request->except('photo', 'password', 'events', 'deleted_events');

        // Handle photo upload
        if ($request->has('photo')) {
            $the_file_path = uploadImage('assets/admin/uploads', $request->photo);
            $userData['photo'] = $the_file_path;
        }

        if ($request->has('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Handle new events
        if ($request->has('events') && is_array($request->events)) {
            foreach ($request->events as $eventData) {
                Event::create([
                    'user_id' => $user->id,
                    'name' => $eventData['name'],
                    'start_date' => $eventData['start_date'],
                    'end_date' => $eventData['end_date'],
                    'commission_percentage' => $eventData['commission_percentage'],
                ]);
            }
        }

        // Handle deleted events
        if ($request->has('deleted_events')) {
            $deletedIds = array_filter(explode(',', $request->deleted_events));
            if (!empty($deletedIds)) {
                Event::whereIn('id', $deletedIds)->delete();
            }
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);


        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}
