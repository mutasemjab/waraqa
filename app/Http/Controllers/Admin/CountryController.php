<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CountrySchedule;
use App\Models\Driver;
use App\Models\DriverAssignment;
use App\Models\DriverSchedule;
use App\Models\Option;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::paginate(10);

        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        
        return view('admin.countries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255|unique:countries,name_ar',
            'name_en' => 'required|string|max:255|unique:countries,name_en',
        ]);

        DB::beginTransaction();
        try {
            // Create country
            $country = Country::create([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en
            ]);

           

            DB::commit();
            return redirect()->route('countries.index')
                ->with('success', __('messages.country_created_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', __('messages.error_creating_country') . ': ' . $e->getMessage());
        }
    }

   

    public function edit(Country $country)
    {        
        return view('admin.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255|unique:countries,name_ar,' . $country->id,
            'name_en' => 'required|string|max:255|unique:countries,name_en,' . $country->id,
        ]);

        DB::beginTransaction();
        try {
            // Update country
            $country->update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en
            ]);

            DB::commit();
            return redirect()->route('countries.index')
                ->with('success', __('messages.country_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', __('messages.error_updating_country') . ': ' . $e->getMessage());
        }
    }
}