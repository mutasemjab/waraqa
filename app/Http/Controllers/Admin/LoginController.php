<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
  public function show_login_view()
  {
    return view('admin.auth.login');
  }

  public function login(LoginRequest $request)
  {
    if (auth()->guard('web')->attempt(['username' => $request->input('username'), 'password' => $request->input('password')])) {
      $user = auth()->user();

      // تحقق من أن المستخدم لديه أي صلاحية تسمح له بدخول لوحة التحكم
      if ($user->getAllPermissions()->isEmpty()) {
        auth()->logout();
        return redirect()->route('admin.showlogin')->with('error', 'Unauthorized access');
      }

      return redirect()->route('admin.dashboard');
    } else {
      return redirect()->route('admin.showlogin');
    }
  }

  public function logout()
  {
    auth()->logout();
    return redirect()->route('admin.showlogin');
  }


  public function editlogin($id)
  {
    $data = User::findOrFail(auth()->id());
    return view('admin.auth.edit', compact('data'));
  }



  public function updatelogin(Request $request, $id)
  {
    $user = User::findOrFail(auth()->id());
    try {
      $user->username = $request->get('username');
      $user->password = Hash::make($request->password);

      if ($user->save()) {
        auth()->logout();
        return redirect()->route('admin.showlogin');
      } else {
        return redirect()->back()->with(['error' => 'Something wrong']);
      }
    } catch (\Exception $ex) {
      return redirect()->back()
        ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
        ->withInput();
    }
  }
}
