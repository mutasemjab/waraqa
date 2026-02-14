<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use App\Models\Country;
use App\Models\SellerSale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index()
    {
        $user = Auth::user();

        // Get user statistics
        $stats = [
            'total_orders' => $user->orders()->count(),
            'pending_orders' => $user->orders()->where('status', '!=', 1)->count(),
            'completed_orders' => $user->orders()->where('status', 1)->count(),
            'total_debt' => $user->userDepts()->sum('remaining_amount'),
            'total_spent' => $user->orders()->sum('total_prices'),
            'paid_orders' => $user->orders()->where('payment_status', 1)->count(),
            'unpaid_orders' => $user->orders()->where('payment_status', 2)->count(),
        ];

        // Get sales statistics
        $salesStats = [
            'total_sales' => SellerSale::count(),
            'total_sales_amount' => SellerSale::sum('total_amount'),
            'this_month_sales' => SellerSale::whereMonth('sale_date', Carbon::now()->month)
                ->whereYear('sale_date', Carbon::now()->year)
                ->count(),
            'this_month_amount' => SellerSale::whereMonth('sale_date', Carbon::now()->month)
                ->whereYear('sale_date', Carbon::now()->year)
                ->sum('total_amount'),
        ];

        return view('user.dashboard', compact('stats', 'salesStats'));
    }

    public function orders(Request $request)
    {
        $user = Auth::user();
        $query = $user->orders()->with(['orderProducts.product', 'userDepts']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Search by order number
        if ($request->filled('search')) {
            $query->where('number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->latest()->paginate(10);

        return view('user.orders', compact('orders'));
    }

    public function orderShow($id)
    {
        $user = Auth::user();
        $order = $user->orders()->with(['orderProducts.product', 'userDepts'])->findOrFail($id);
        
        return view('user.order-details', compact('order'));
    }

    public function debts(Request $request)
    {
        $user = Auth::user();
        $query = $user->userDepts()->with('order');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $debts = $query->latest()->paginate(10);
        $totalDebt = $user->userDepts()->where('status', 1)->sum('remaining_amount');

        return view('user.debts', compact('debts', 'totalDebt'));
    }

    public function profile()
    {
        $user = Auth::user();
        $countries = Country::all();
        return view('user.profile', compact('user', 'countries'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public/users', $photoName);
            $updateData['photo'] = 'users/' . $photoName;
        }

        $user->update($updateData);

        return back()->with('success', __('messages.profile_updated_successfully'));
    }


    // Generate user report for admin
    public function generateReport()
    {
        $user = Auth::user();
        
        // Comprehensive user activity report
        $report = [
            'user_info' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'member_since' => $user->created_at->format('Y-m-d'),
                'last_order' => $user->orders()->latest()->first()?->date,
            ],
            'order_summary' => [
                'total_orders' => $user->orders()->count(),
                'total_spent' => $user->orders()->sum('total_prices'),
                'average_order_value' => $user->orders()->avg('total_prices'),
                'pending_orders' => $user->orders()->where('status', 1)->count(),
                'completed_orders' => $user->orders()->where('status', 2)->count(),
                'cancelled_orders' => $user->orders()->where('status', 6)->count(),
            ],
            'payment_summary' => [
                'total_debt' => $user->userDepts()->where('status', 1)->sum('remaining_amount'),
                'total_paid' => $user->orders()->sum('paid_amount'),
                'payment_completion_rate' => $user->orders()->count() > 0 ? 
                    ($user->orders()->where('payment_status', 1)->count() / $user->orders()->count()) * 100 : 0,
            ],
            'top_products' => OrderProduct::select('products.name_ar', 'products.name_en')
                ->selectRaw('SUM(order_products.quantity) as total_quantity')
                ->selectRaw('SUM(order_products.total_price_after_tax) as total_spent')
                ->join('products', 'order_products.product_id', '=', 'products.id')
                ->join('orders', 'order_products.order_id', '=', 'orders.id')
                ->where('orders.user_id', $user->id)
                ->groupBy('products.id', 'products.name_ar', 'products.name_en')
                ->orderBy('total_quantity', 'desc')
                ->take(5)
                ->get(),
            'recent_activity' => $user->orders()
                ->with('orderProducts.product')
                ->latest()
                ->take(10)
                ->get(),
        ];

        return view('user.report', compact('report'));
    }

    // Notifications
    public function notifications()
    {
        $user = Auth::user();
        
        $notifications = collect();
        
        // Debt reminders
        $activeDebts = $user->userDepts()->where('status', 1)->with('order')->get();
        foreach ($activeDebts as $debt) {
            $notifications->push([
                'type' => 'debt',
                'title' => __('messages.payment_reminder'),
                'message' => __('messages.outstanding_debt_for_order', ['order' => $debt->order->number, 'amount' => number_format($debt->remaining_amount, 2)]),
                'date' => $debt->created_at,
                'icon' => 'fas fa-exclamation-triangle',
                'class' => 'warning'
            ]);
        }
        
        // Recent order updates
        $recentOrders = $user->orders()->where('updated_at', '>=', Carbon::now()->subWeek())->get();
        foreach ($recentOrders as $order) {
            $notifications->push([
                'type' => 'order',
                'title' => __('messages.order_update'),
                'message' => __('messages.order_status_updated', ['order' => $order->number]),
                'date' => $order->updated_at,
                'icon' => 'fas fa-shopping-cart',
                'class' => 'info'
            ]);
        }
        
        $notifications = $notifications->sortByDesc('date')->take(20);
        
        return view('user.notifications', compact('notifications'));
    }
}