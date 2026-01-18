<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Event;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class SellerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:seller-table')->only(['index']);
        $this->middleware('permission:seller-add')->only(['create', 'store']);
        $this->middleware('permission:seller-edit')->only(['edit', 'update']);
        $this->middleware('permission:seller-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellers = User::role('seller')->get();
        return view('admin.sellers.index', compact('sellers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::get();
        return view('admin.sellers.create', compact('countries'));
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
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'events' => 'nullable|array',
            'events.*.name' => 'required_with:events|string|max:255',
            'events.*.start_date' => 'required_with:events|date_format:Y-m-d\TH:i',
            'events.*.end_date' => 'required_with:events|date_format:Y-m-d\TH:i|after:events.*.start_date',
            'events.*.commission_percentage' => 'required_with:events|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('sellers.create')
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

        $seller = User::create($userData);

        // Assign seller role
        $seller->assignRole('seller');

        // Create warehouse for seller
        Warehouse::create([
            'name' => $seller->name,
            'user_id' => $seller->id,
        ]);

        // Create events if provided
        if ($request->has('events') && is_array($request->events)) {
            foreach ($request->events as $eventData) {
                Event::create([
                    'user_id' => $seller->id,
                    'name' => $eventData['name'],
                    'start_date' => $eventData['start_date'],
                    'end_date' => $eventData['end_date'],
                    'commission_percentage' => $eventData['commission_percentage'],
                ]);
            }
        }

        return redirect()
            ->route('sellers.index')
            ->with('success', 'Seller created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seller = User::role('seller')->findOrFail($id);

        return view('admin.sellers.show', compact('seller'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $seller = User::with('events')->role('seller')->findOrFail($id);
        $countries = Country::get();
        return view('admin.sellers.edit', compact('seller', 'countries'));
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
        $seller = User::role('seller')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'password' => 'nullable',
            'phone' => 'sometimes|string|unique:users,phone,' . $id,
            'email' => 'nullable|email|unique:users,email,' . $id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'fcm_token' => 'nullable|string',
            'activate' => 'nullable|in:1,2',
            'country_id' => 'nullable|exists:countries,id',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'events' => 'nullable|array',
            'events.*.name' => 'required_with:events|string|max:255',
            'events.*.start_date' => 'required_with:events|date_format:Y-m-d\TH:i',
            'events.*.end_date' => 'required_with:events|date_format:Y-m-d\TH:i|after:events.*.start_date',
            'events.*.commission_percentage' => 'required_with:events|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('sellers.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $userData = $request->except('photo', 'password', 'events', 'deleted_events', 'updated_events');

        // Handle photo upload
        if ($request->has('photo')) {
            $the_file_path = uploadImage('assets/admin/uploads', $request->photo);
            $userData['photo'] = $the_file_path;
        }

        if ($request->has('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $seller->update($userData);

        // Handle updated events
        if ($request->has('updated_events') && !empty($request->updated_events)) {
            $updatedEvents = json_decode($request->updated_events, true);
            if (is_array($updatedEvents)) {
                foreach ($updatedEvents as $eventData) {
                    if (isset($eventData['id'])) {
                        Event::where('id', $eventData['id'])->where('user_id', $seller->id)->update([
                            'name' => $eventData['name'],
                            'start_date' => $eventData['start_date'],
                            'end_date' => $eventData['end_date'],
                            'commission_percentage' => $eventData['commission_percentage'],
                        ]);
                    }
                }
            }
        }

        // Handle new events
        if ($request->has('events') && is_array($request->events)) {
            foreach ($request->events as $eventData) {
                Event::create([
                    'user_id' => $seller->id,
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
            ->route('sellers.index')
            ->with('success', 'Seller updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $seller = User::role('seller')->findOrFail($id);

        $seller->delete();

        return redirect()
            ->route('sellers.index')
            ->with('success', 'Seller deleted successfully');
    }

    /**
     * Search for sellers (API endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $term = $request->get('term', '');
        $limit = $request->get('limit', 5);

        $sellers = User::role('seller')
            ->where('activate', 1)
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            })
            ->limit($limit)
            ->get()
            ->map(function ($seller) {
                return [
                    'id' => $seller->id,
                    'text' => $seller->name,
                    'commission_percentage' => $seller->commission_percentage
                ];
            });

        return response()->json($sellers);
    }
}