<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:setting-table')->only(['index']);
        $this->middleware('permission:setting-edit')->only(['edit', 'update']);
    }

  public function index()
  {

    $data = Setting::paginate(PAGINATION_COUNT);

    return view('admin.settings.index', ['data' => $data]);
  }



  public function edit($id)
  {
       if (auth()->user()->can('setting-edit')) {
    $data=Setting::findorFail($id);
    return view('admin.settings.edit',compact('data'));
       }else{
            return redirect()->back()
            ->with('error', "Access Denied");
       }
  }

  public function update(Request $request, $id)
  {
      if (auth()->user()->can('setting-edit')) {
          $setting = Setting::findOrFail($id);

          $request->validate([
              'value' => 'required|string',
          ]);

          try {
              $setting->value = $request->input('value');

              if ($setting->save()) {
                  return redirect()->route('settings.index')->with('success', 'تم تحديث الإعداد بنجاح');
              } else {
                  return redirect()->back()->with('error', 'حدث خطأ أثناء التحديث');
              }
          } catch (\Exception $ex) {
              return redirect()->back()
                  ->with('error', 'خطأ: ' . $ex->getMessage())
                  ->withInput();
          }
      } else {
          return redirect()->back()->with('error', "Access Denied");
      }
  }







}
