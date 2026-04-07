<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string',
            'review_text' => 'required|string'
        ]);

        $user = $request->user();
        
        // Check if user has actually purchased this product
        $hasPurchased = \App\Models\Order::where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->whereHas('items', function($query) use ($request) {
                $query->where('product_id', $request->product_id);
            })->exists();

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'title' => $request->title,
            'review_text' => $request->review_text,
            'is_verified_purchase' => $hasPurchased,
            'is_approved' => false // Default to false for moderation
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted and is awaiting moderation.',
            'data' => $review->load('user')
        ], 201);
    }

    public function getProductReviews($productId)
    {
        $reviews = Review::where('product_id', $productId)
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->paginate(5);

        $product = Product::findOrFail($productId);
        $avgRating = $product->averageRating();
        $totalReviews = $product->reviews()->where('is_approved', true)->count();
        
        $ratingBreakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingBreakdown[$i] = $product->reviews()
                ->where('is_approved', true)
                ->where('rating', $i)
                ->count();
        }

        return response()->json([
            'reviews' => $reviews,
            'average_rating' => round($avgRating, 1),
            'total_reviews' => $totalReviews,
            'rating_breakdown' => $ratingBreakdown
        ]);
    }
}
