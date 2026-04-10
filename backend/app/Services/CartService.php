<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Exception;

class CartService
{
    /**
     * Get user's cart or create one
     */
    public function getCart(User $user): Cart
    {
        $cart = $user->cart;
        if (!$cart) {
            $cart = $user->cart()->create();
        }
        return $cart;
    }

    /**
     * Add item to cart with stock validation
     */
    public function addItem(User $user, int $productId, int $quantity): CartItem
    {
        $product = Product::findOrFail($productId);
        $cart = $this->getCart($user);

        // Stock validation
        if ($product->track_inventory) {
            if ($product->stock <= 0) {
                throw new Exception('Product out of stock');
            }

            $currentInCart = $cart->items()->where('product_id', $productId)->first();
            $totalQuantity = ($currentInCart ? $currentInCart->quantity : 0) + $quantity;

            if ($totalQuantity > $product->stock) {
                throw new Exception("Requested quantity exceeds available stock ({$product->stock})");
            }
        }

        // Add or update
        $cartItem = $cart->items()->updateOrCreate(
            ['product_id' => $productId],
            ['quantity' => \DB::raw("quantity + $quantity")]
        );

        // Refresh to get actual quantity if updated via DB::raw
        return $cartItem->refresh();
    }

    /**
     * Update item quantity
     */
    public function updateItem(User $user, int $itemId, int $quantity): CartItem
    {
        $cart = $this->getCart($user);
        $cartItem = $cart->items()->findOrFail($itemId);
        $product = $cartItem->product;

        // Stock validation
        if ($product->track_inventory && $quantity > $product->stock) {
            throw new Exception("Requested quantity exceeds available stock ({$product->stock})");
        }

        $cartItem->update(['quantity' => $quantity]);
        return $cartItem;
    }

    /**
     * Remove item from cart
     */
    public function removeItem(User $user, int $itemId): bool
    {
        $cart = $this->getCart($user);
        return $cart->items()->where('id', $itemId)->delete() > 0;
    }

    /**
     * Clear entire cart
     */
    public function clear(User $user): void
    {
        $cart = $this->getCart($user);
        $cart->items()->delete();
    }

    /**
     * Calculate cart totals
     */
    public function getTotals(User $user): array
    {
        $cart = $this->getCart($user);
        $items = $cart->items()->with('product')->get();

        $subtotal = $items->sum(fn ($item) => $item->product->price * $item->quantity);
        $count = $items->sum('quantity');

        return [
            'subtotal' => $subtotal,
            'count' => $count,
            'items' => $items
        ];
    }
}
