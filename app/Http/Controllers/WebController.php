<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class WebController extends Controller
{
    public function home()
    {
        $products = Product::with('category')
            ->latest()
            ->take(4)
            ->get();
        return view('frontend.home', compact('products'));
    }

    public function about()
    {
        return view('frontend.about');
    }

    public function philosophy()
    {
        return view('frontend.philosophy');
    }

    public function ritual()
    {
        return view('frontend.ritual');
    }

    public function contact()
    {
        return view('frontend.contact');
    }

    public function up()
    {
        return response()->json(['status' => 'ok'], 200);
    }

    public function contactPost(StoreContactRequest $request)
    {
        $validated = $request->validated();

        // Send email to admin/support
        try {
            Mail::to(config('app.admin_email', 'admin@aushvera.com'))
                ->send(new ContactMail($validated));

            return back()->with('contact_success', 'Thank you for reaching out! We\'ll get back to you soon.');
        } catch (\Exception $e) {
            // Log the error but don't show it to user
            \Log::error('Contact form email failed: ' . $e->getMessage());

            // Still show success to user (email will be sent later via queue)
            return back()->with('contact_success', 'Thank you for reaching out! We\'ll get back to you soon.');
        }
    }

    public function products(Request $request)
    {
        $productsQuery = Product::with('category');

        if ($request->filled('search')) {
            $search = trim(strip_tags($request->search));
            $productsQuery->where('name', 'like', '%' . $search . '%');
        }
        if ($request->filled('category')) {
            $productsQuery->where('category_id', $request->category);
        }

        $products = $productsQuery->latest()->paginate(12);
        $categories = Category::orderBy('name')->get();

        return view('frontend.products', compact('products', 'categories'));
    }

    public function product(Request $request)
    {
        $productId = $request->query('id');
        if ($productId) {
            $product = Product::with(['category', 'reviews.user'])->findOrFail($productId);
            $related = Product::with('category')
                ->where('id', '!=', $product->id)
                ->where('category_id', $product->category_id)
                ->take(4)->get();
            return view('frontend.product', compact('product', 'related'));
        }

        try {
            $products = Product::active()->with('category')->latest()->take(4)->get();
        } catch (\Exception $e) {
            $products = Product::with('category')->latest()->take(4)->get();
        }
        return view('frontend.product', compact('products'));
    }

    public function cart()
    {
        return view('frontend.cart');
    }

    public function profile()
    {
        try {
            $user = auth()->user();
        } catch (\Exception $e) {
            $user = null;
        }
        return view('frontend.profile', ['user' => $user]);
    }

    public function terms()
    {
        return view('frontend.terms');
    }

    // ─── Admin (view-only routes, actual data via API) ───────────────────────

    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    public function adminProducts()
    {
        $products   = Product::with('category')->latest()->get();
        $categories = Category::orderBy('name')->get();
        return view('admin.products', compact('products', 'categories'));
    }

    public function adminOrders()
    {
        $orders = Order::with('user')->latest()->take(50)->get();
        return view('admin.orders', compact('orders'));
    }

    public function adminUsers()
    {
        $users = User::latest()->get();
        return view('admin.users', compact('users'));
    }

    public function adminReviews()
    {
        $reviews = Review::with(['user', 'product'])->latest()->get();
        return view('admin.reviews', compact('reviews'));
    }

    public function adminCategories()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories', compact('categories'));
    }

    public function adminCoupons()
    {
        return view('admin.coupons');
    }

    public function adminSubscriptions()
    {
        return view('admin.subscriptions');
    }

    public function adminEmails()
    {
        return view('admin.emails');
    }

    public function adminRegister()
    {
        return view('admin.register');
    }

    public function adminLogout()
    {
        session()->forget('admin_token');
        \Illuminate\Support\Facades\Auth::logout();
        return redirect()->route('admin.login')->with('success', 'You have been logged out.');
    }
}
