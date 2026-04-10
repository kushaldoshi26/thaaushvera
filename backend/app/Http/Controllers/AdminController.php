<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Show the admin login page
     */
    public function showLogin()
    {
        if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'super_admin')) {
            return redirect(url('/admin'));
        }
        return view('admin.login');
    }

    /**
     * Handle admin login form submission
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        if (!in_array($user->role, ['admin', 'super_admin'])) {
            return back()->withErrors(['email' => 'You do not have admin access.'])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors(['email' => 'Your account is deactivated. Contact super admin.'])->withInput();
        }

        Auth::login($user);
        $user->update(['last_login_at' => now()]);

        // Also store a Sanctum token in session for JS API calls
        $token = $user->createToken('admin_session')->plainTextToken;
        session(['admin_token' => $token]);

        return redirect(url('/admin'));
    }

    public function dashboard()
    {
        $totalOrders = \App\Models\Order::count();
        $totalRevenue = \App\Models\Order::where('payment_status', 'paid')->sum('total_amount');
        $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
        $totalProducts = \App\Models\Product::count();
        $lowStockProducts = \App\Models\Product::where('stock', '<', 10)->where('stock', '>', 0)->count();
        $outOfStockProducts = \App\Models\Product::where('stock', 0)->count();
        
        // Today's stats
        $todayOrders = \App\Models\Order::whereDate('created_at', today())->count();
        $todayRevenue = \App\Models\Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')->sum('total_amount');
        
        // Monthly revenue (last 6 months)
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $monthFormat = $isSqlite ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";
        $dayFormat = $isSqlite ? "strftime('%Y-%m-%d', created_at)" : "DATE_FORMAT(created_at, '%Y-%m-%d')";

        $monthlyRevenue = \App\Models\Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("$monthFormat as month, SUM(total_amount) as revenue")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Daily sales (last 30 days)
        $dailySales = \App\Models\Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw("$dayFormat as date, SUM(total_amount) as revenue, COUNT(*) as orders")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Exact last 7 days counts for bar chart
        $last7DaysOrders = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = \App\Models\Order::whereDate('created_at', $date)->count();
            $last7DaysOrders[] = $count;
        }

        // Monthly users (simulated since user table lacks monthly history for now)
        $monthlyUsers = [0, 0, 0, 0, 0, User::where('role', 'user')->count()];

        // Recent Orders
        $recentOrders = \App\Models\Order::with('user')->latest()->take(5)->get();

        // Status counts
        $statusCounts = [
            'pending' => \App\Models\Order::where('status', 'pending')->count(),
            'processing' => \App\Models\Order::where('status', 'processing')->count(),
            'shipped' => \App\Models\Order::where('status', 'shipped')->count(),
            'delivered' => \App\Models\Order::where('status', 'delivered')->count(),
            'cancelled' => \App\Models\Order::where('status', 'cancelled')->count(),
        ];

        // Top Selling Products
        $topSellingProducts = \App\Models\OrderItem::selectRaw('product_id, SUM(quantity) as sold')
            ->groupBy('product_id')
            ->orderByDesc('sold')
            ->take(5)
            ->with('product')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->product_id,
                    'name' => $item->product->name ?? 'Unknown',
                    'sold' => (int) $item->sold
                ];
            });

        return response()->json([
            'total_users' => User::where('role', 'user')->count(),
            'total_orders' => $totalOrders,
            'total_revenue' => (float) $totalRevenue,
            'pending_orders' => $pendingOrders,
            'total_products' => $totalProducts,
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_products' => $outOfStockProducts,
            'today_orders' => $todayOrders,
            'today_revenue' => (float) $todayRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'daily_sales' => $dailySales,
            'daily_orders' => $last7DaysOrders,
            'monthly_users' => $monthlyUsers,
            'recent_orders' => $recentOrders,
            'status_counts' => $statusCounts,
            'top_selling_products' => $topSellingProducts,
        ]);
    }

    public function users()
    {
        $users = User::where('role', 'user')
            ->withCount('orders')
            ->latest()
            ->get();
        return response()->json(['users' => $users]);
    }

    public function getUser($id)
    {
        $user = User::withCount('orders')->findOrFail($id);
        return response()->json(['user' => $user]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $id,
            'phone'    => 'nullable|string|max:20',
            'city'     => 'nullable|string|max:100',
            'state'    => 'nullable|string|max:100',
            'pincode'  => 'nullable|string|max:10',
            'address'  => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $user->update($validated);
        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function reviews(Request $request)
    {
        $reviews = Review::with(['user', 'product'])
            ->when($request->status, function($query) use ($request) {
                if ($request->status === 'approved') return $query->where('is_approved', true);
                if ($request->status === 'pending') return $query->where('is_approved', false);
            })
            ->latest()
            ->paginate(20);

        return response()->json($reviews);
    }

    public function updateReviewStatus(Request $request, $id)
    {
        $request->validate([
            'is_approved' => 'required|boolean'
        ]);

        $review = Review::findOrFail($id);
        $review->update([
            'is_approved' => $request->is_approved
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review status updated successfully.',
            'data' => $review
        ]);
    }

    public function deleteReview($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully.'
        ]);
    }

    public function updatePricing(Request $request, $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $product->update($request->only(['price', 'original_price', 'discount']));
        return response()->json(['message' => 'Pricing updated', 'product' => $product]);
    }

    public function loginHistory()
    {
        $history = DB::table('login_history')
            ->join('users', 'login_history.user_id', '=', 'users.id')
            ->select('users.name', 'users.email', 'users.role', 'login_history.login_time', 'login_history.ip_address')
            ->orderBy('login_history.login_time', 'desc')
            ->get();
        return response()->json($history);
    }

    /**
     * Admin: list orders with pagination, filters and export
     */
    public function orders(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);
        $status = $request->query('status');
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');

        $query = \App\Models\Order::with('user', 'items.product');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            })->orWhere('id', $search);
        }

        $totalFound = $query->count();
        $orders = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'total_found' => $totalFound,
            'data' => $orders->items(),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ]
        ], 200);
    }

    public function showOrder($id)
    {
        $order = \App\Models\Order::with(['user', 'items.product'])->find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id'             => $order->id,
                'total_amount'   => $order->total_amount,
                'status'         => $order->status,
                'payment_status' => $order->payment_status ?? 'pending',
                'payment_method' => $order->payment_method,
                'transaction_id' => $order->transaction_id,
                'user'           => $order->user ? [
                    'id'    => $order->user->id,
                    'name'  => $order->user->name,
                    'email' => $order->user->email,
                    'phone' => $order->user->phone,
                ] : null,
                'items'          => $order->items->map(function ($item) {
                    return [
                        'id'         => $item->id,
                        'product_id' => $item->product_id,
                        'product'    => $item->product ? ['id' => $item->product->id, 'name' => $item->product->name] : null,
                        'quantity'   => $item->quantity,
                        'price'      => $item->price,
                    ];
                }),
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]
        ], 200);
    }

    /**
     * Export orders as CSV
     */
    public function exportOrders(Request $request)
    {
        $fileName = 'orders_export_' . date('Ymd_His') . '.csv';
        $orders = \App\Models\Order::with('user')->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($orders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order ID', 'User', 'Email', 'Total', 'Status', 'Payment Status', 'Created At']);
            foreach ($orders as $order) {
                fputcsv($out, [
                    $order->id,
                    $order->user->name ?? '',
                    $order->user->email ?? '',
                    $order->total_amount,
                    $order->status,
                    $order->payment_status ?? '',
                    $order->created_at,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

}
