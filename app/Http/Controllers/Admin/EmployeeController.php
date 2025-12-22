<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:employee-table')->only(['index']);
        $this->middleware('permission:employee-add')->only(['create', 'store']);
        $this->middleware('permission:employee-edit')->only(['edit', 'update']);
        $this->middleware('permission:employee-delete')->only(['show', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = User::whereIsAdmin()
            ->where('id', '!=', auth()->user()->id);

        if ($request->search != '' || $request->search) {
            $data->where(function ($query) use ($request) {
                $query->where('users.name', 'LIKE', "%$request->search%")
                    ->orWhere('users.email', 'LIKE', "%$request->search%")
                    ->orWhere('users.phone', 'LIKE', "%$request->search%");
            });
        }
        $data = $data->paginate(10);
        return view('admin.employee.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (auth()->user()->can('employee-add')) {
            $roles = Role::where('guard_name', 'web')->get();
            return view('admin.employee.create', compact('roles'));
        } else {
            return redirect()->back()
                ->with('error', "Access Denied");
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->user()->can('employee-add')) {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|unique:users,email',
                'password' => 'required',
                'roles' => 'required'
            ]);

            DB::beginTransaction();
            try {
                $user = new User([
                    'name' => $request->name,
                    'email' => $request->email,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'activate' => 1,
                ]);

                $user->save();
                $user->syncRoles($request->roles);

                DB::commit();
                return redirect()->route('admin.employee.index')
                    ->with('success', 'Employee created successfully');
            } catch (Exception $e) {
                DB::rollBack();
                Log::info("Error Occured", ['message' => $e]);
                return redirect()->route('admin.employee.index')
                    ->with('error', 'Something Wrong');
            }
        } else {
            return redirect()->back()
                ->with('error', "Access Denied");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (auth()->user()->can('employee-delete')) {
            DB::beginTransaction();
            try {
                $user = User::find($id);
                $user->syncRoles([]);
                $user->delete();
                DB::commit();
                return redirect()->route('admin.employee.index')
                    ->with('success', 'Admin deleted successfully');
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->route('admin.employee.index')
                    ->with('error', 'Something Error');
            }
        } else {
            return redirect()->back()
                ->with('error', "Access Denied");
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (auth()->user()->can('employee-edit')) {
            $user = User::find($id);
            $roles = Role::where('guard_name', 'web')->get();
            $userRole = $user->roles->pluck('id')->all();
            return view('admin.employee.edit', compact('user', 'roles', 'userRole'));
        } else {
            return redirect()->back()
                ->with('error', "Access Denied");
        }
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
        if (auth()->user()->can('employee-edit')) {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|unique:users,email,' . $id,
                'roles' => 'required'
            ]);

            DB::beginTransaction();
            try {
                $user = User::find($id);

                $user->name = $request->name;
                $user->email = $request->email;
                $user->username = $request->username;
                if ($request->password) {
                    $user->password = Hash::make($request->password);
                }
                $user->save();
                $user->syncRoles($request->roles);

                DB::commit();
                return redirect()->route('admin.employee.index')
                    ->with('success', 'Employee updated successfully');
            } catch (Exception $e) {
                DB::rollBack();
                Log::info("Error Occured", ['message' => $e]);
                return redirect()->route('admin.employee.index')
                    ->with('error', 'Something Wrong');
            }
        } else {
            return redirect()->back()
                ->with('error', "Access Denied");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            $user->syncRoles([]);
            $user->delete();
            DB::commit();
            return redirect()->route('admin.employee.index')
                ->with('success', 'Employee deleted successfully');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('admin.employee.index')
                ->with('error', 'Something Error');
        }
    }
}