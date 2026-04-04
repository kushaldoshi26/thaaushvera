<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;

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

    public function contactPost(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'message' => 'required|string|max:2000',
        ]);
        // Future: Mail::to('aushveraglobalbiz1718@gmail.com')->send(new ContactMail($request->all()));
        return back()->with('contact_success', 'Thank you! We\'ll get back to you soon.');
    }

    public function products(Request $request)
    {
        $productsQuery = Product::with('category');

        if ($request->filled('search')) {
            $productsQuery->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $productsQuery->where('category_id', $request->category);
        }

        $products = $productsQuery->latest()->get();
        $categories = Category::orderBy('name')->get();

        return view('frontend.products', compact('products', 'categories'));
    }

    public function product(Request $request)
    {
        $productId = $request->query('id');
        if ($productId) {
            $product = Product::with(['category', 'reviews.user'])->find($productId);
            if (!$product) {
                $product  = null;
                $products = Product::with('category')->latest()->take(4)->get();
                return view('frontend.product', compact('products'));
            }
            $related = Product::with('category')
                ->where('id', '!=', $product->id)
                ->where('category_id', $product->category_id)
                ->take(4)->get();
            return view('frontend.product', compact('product', 'related'));
        }

        $products = Product::with('category')->where('is_active', true)->latest()->take(4)->get();
        return view('frontend.product', compact('products'));
    }

    public function cart()
    {
        return view('frontend.cart');
    }

    public function profile()
    {
        return view('frontend.profile');
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

    public function adminRegister()
    {
        return view('admin.register');
    }
}
