<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(\App\Services\CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Get user's cart with items
     */
    public function show(Request $request)
    {
        try {
            $totals = $this->cartService->getTotals($request->user());

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $totals['items']->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'cart_item_id' => $item->id,
                            'product_id' => $item->product_id,
                            'product' => [
                                'id' => $item->product->id,
                                'name' => $item->product->name,
                                'price' => $item->product->price,
                                'image' => $item->product->image,
                            ],
                            'quantity' => $item->quantity,
                            'subtotal' => $item->product->price * $item->quantity,
                        ];
                    }),
                    'total_price' => $totals['subtotal'],
                    'total_items' => $totals['count'],
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add item to cart with stock validation
     */
    public function addItem(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $cartItem = $this->cartService->addItem(
                $request->user(),
                $validated['product_id'],
                $validated['quantity']
            );

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart',
                'data' => [
                    'id' => $cartItem->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $cartItem->product->price * $cartItem->quantity,
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Update cart item quantity with stock validation and ownership check
     */
    public function updateItem(Request $request, $itemId)
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $cartItem = $this->cartService->updateItem(
                $request->user(),
                $itemId,
                $validated['quantity']
            );

            return response()->json([
                'success' => true,
                'message' => 'Cart item updated',
                'data' => [
                    'id' => $cartItem->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $cartItem->product->price * $cartItem->quantity,
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Remove item from cart with ownership check
     */
    public function removeItem(Request $request, $itemId)
    {
        try {
            $this->cartService->removeItem($request->user(), $itemId);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        try {
            $this->cartService->clear($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cart count
     */
    public function count(Request $request)
    {
        try {
            $totals = $this->cartService->getTotals($request->user());

            return response()->json([
                'success' => true,
                'data' => [
                    'count' => (int) $totals['count']
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get cart count',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
