<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\UserDept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserDeptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = UserDept::with(['user', 'order']);

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by user name
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $userDepts = $query->orderBy('created_at', 'desc')->paginate(15);
        $users = User::all();

        return view('admin.user_depts.index', compact('userDepts', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $orders = Order::with('user')->where('remaining_amount', '>', 0)->get();
        
        return view('admin.user_depts.create', compact('users', 'orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'order_id' => 'required|exists:orders,id',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:1,2'
        ]);

        $paidAmount = $request->paid_amount ?? 0;
        $remainingAmount = $request->total_amount - $paidAmount;

        DB::beginTransaction();
        try {
            UserDept::create([
                'user_id' => $request->user_id,
                'order_id' => $request->order_id,
                'total_amount' => $request->total_amount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'status' => $remainingAmount > 0 ? 1 : 2
            ]);

            DB::commit();
            return redirect()->route('user_depts.index')->with('success', __('messages.user_dept_created_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', __('messages.error_creating_user_dept') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UserDept $userDept)
    {
        $userDept->load(['user', 'order.orderProducts.product']);
        return view('admin.user_depts.show', compact('userDept'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserDept $userDept)
    {
        $users = User::all();
        $orders = Order::with('user')->get();
        
        return view('admin.user_depts.edit', compact('userDept', 'users', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserDept $userDept)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'order_id' => 'required|exists:orders,id',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:1,2'
        ]);

        $paidAmount = $request->paid_amount ?? 0;
        $remainingAmount = $request->total_amount - $paidAmount;

        DB::beginTransaction();
        try {
            $userDept->update([
                'user_id' => $request->user_id,
                'order_id' => $request->order_id,
                'total_amount' => $request->total_amount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'status' => $remainingAmount > 0 ? 1 : 2
            ]);

            DB::commit();
            return redirect()->route('user_depts.index')->with('success', __('messages.user_dept_updated_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', __('messages.error_updating_user_dept') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserDept $userDept)
    {
        try {
            $userDept->delete();
            return redirect()->route('user_depts.index')->with('success', __('messages.user_dept_deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('messages.error_deleting_user_dept') . ': ' . $e->getMessage());
        }
    }

    /**
     * Make payment for user debt
     */
    public function makePayment(Request $request, UserDept $userDept)
    {
        $request->validate([
            'payment_amount' => 'required|numeric|min:0.01|max:' . $userDept->remaining_amount
        ]);

        DB::beginTransaction();
        try {
            $newPaidAmount = $userDept->paid_amount + $request->payment_amount;
            $newRemainingAmount = $userDept->total_amount - $newPaidAmount;

            $userDept->update([
                'paid_amount' => $newPaidAmount,
                'remaining_amount' => $newRemainingAmount,
                'status' => $newRemainingAmount <= 0 ? 2 : 1
            ]);

            // Update related order
            $order = $userDept->order;
            if ($order) {
                $order->update([
                    'paid_amount' => $order->paid_amount + $request->payment_amount,
                    'remaining_amount' => $order->remaining_amount - $request->payment_amount,
                    'payment_status' => ($order->remaining_amount - $request->payment_amount) <= 0 ? 1 : 2
                ]);
            }

            DB::commit();
            return redirect()->route('user_depts.show', $userDept)->with('success', __('messages.payment_recorded_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', __('messages.error_recording_payment') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get user debts summary
     */
    public function userSummary($userId)
    {
        $user = User::findOrFail($userId);
        $totalDebt = UserDept::where('user_id', $userId)->sum('remaining_amount');
        $activeDebts = UserDept::where('user_id', $userId)->where('status', 1)->count();
        $paidDebts = UserDept::where('user_id', $userId)->where('status', 2)->count();

        return response()->json([
            'user' => $user,
            'total_debt' => $totalDebt,
            'active_debts' => $activeDebts,
            'paid_debts' => $paidDebts
        ]);
    }
}