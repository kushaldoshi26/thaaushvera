<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'original_price', 'discount', 'image', 'display_images', 'stock', 'low_stock_threshold', 'track_inventory', 'category', 'category_id'];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockHistory()
    {
        return $this->hasMany(StockHistory::class);
    }

    // ─── Query Scopes (Reusable Queries) ─────────────────────────────────────

    /**
     * Scope: Only active products
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: With relations for frontend display
     */
    public function scopeWithRelations(Builder $query)
    {
        return $query->with('category', 'reviews.user');
    }

    /**
     * Scope: Search by name or description
     */
    public function scopeSearch(Builder $query, ?string $term)
    {
        if (!$term) return $query;
        
        $term = trim(strip_tags($term));
        return $query->where('name', 'like', "%{$term}%")
                     ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope: Filter by category
     */
    public function scopeByCategory(Builder $query, ?int $categoryId)
    {
        return $categoryId ? $query->where('category_id', $categoryId) : $query;
    }

    /**
     * Scope: Popular products (most reviews)
     */
    public function scopePopular(Builder $query)
    {
        return $query->withCount('reviews')
            ->orderBy('reviews_count', 'desc');
    }

    // ─── Methods ──────────────────────────────────────────────────────────────

    public function averageRating()
    {
        return $this->reviews()->where('is_approved', true)->avg('rating');
    }

    public function isLowStock()
    {
        return $this->track_inventory && $this->stock <= $this->low_stock_threshold;
    }

    public function isOutOfStock()
    {
        return $this->track_inventory && $this->stock <= 0;
    }

    public function hasStock($quantity = 1)
    {
        return !$this->track_inventory || $this->stock >= $quantity;
    }
}
