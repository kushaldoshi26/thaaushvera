<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create an order from a user's cart
     */
    public function createFromCart(User $user): Order
    {
        $cart = $user->cart;
        if (!$cart || $cart->items->isEmpty()) {
            throw new Exception('Cart is empty');
        }

        return DB::transaction(function () use ($user, $cart) {
            $totalAmount = 0;
            $itemsData = [];

            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;

                // Final stock check with lock
                $lockedProduct = Product::lockForUpdate()->find($product->id);
                if ($lockedProduct->track_inventory && $lockedProduct->stock < $cartItem->quantity) {
                    throw new Exception("Insufficient stock for {$lockedProduct->name}");
                }

                $subtotal = $lockedProduct->price * $cartItem->quantity;
                $totalAmount += $subtotal;

                $itemsData[] = [
                    'product_id' => $lockedProduct->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $lockedProduct->price,
                    'lockedProduct' => $lockedProduct
                ];
            }

            // Create Order
            $order = $user->orders()->create([
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'pending'
            ]);

            // Create Items & Update Stock
            foreach ($itemsData as $data) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $data['product_id'],
                    'quantity' => $data['quantity'],
                    'price' => $data['price']
                ]);

                if ($data['lockedProduct']->track_inventory) {
                    $stockBefore = $data['lockedProduct']->stock;
                    $data['lockedProduct']->decrement('stock', $data['quantity']);
                    
                    StockHistory::create([
                        'product_id' => $data['product_id'],
                        'quantity_change' => -$data['quantity'],
                        'stock_before' => $stockBefore,
                        'stock_after' => $data['lockedProduct']->stock,
                        'type' => 'sale',
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'user_id' => $user->id
                    ]);
                }
            }

            // Clear Cart
            $cart->items()->delete();

            return $order;
        });
    }

    /**
     * Cancel an order and restore stock
     */
    public function cancelOrder(Order $order): void
    {
        if (in_array($order->status, ['shipped', 'delivered', 'cancelled'])) {
            throw new Exception("Cannot cancel order with status: {$order->status}");
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product->track_inventory) {
                    $stockBefore = $product->stock;
                    $product->increment('stock', $item->quantity);

                    StockHistory::create([
                        'product_id' => $product->id,
                        'quantity_change' => $item->quantity,
                        'stock_before' => $stockBefore,
                        'stock_after' => $product->stock,
                        'type' => 'return',
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'user_id' => $order->user_id,
                        'notes' => 'Order cancelled'
                    ]);
                }
            }

            $order->update(['status' => 'cancelled']);
        });
    }

    /**
     * Transition order status
     */
    public function updateStatus(Order $order, string $status, ?string $paymentStatus = null): void
    {
        $data = ['status' => $status];
        if ($paymentStatus) {
            $data['payment_status'] = $paymentStatus;
        }
        $order->update($data);
    }
}
