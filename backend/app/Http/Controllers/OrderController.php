<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(\App\Services\OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Get user's orders
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $orders = $user->orders()->orderBy('created_at', 'desc')->get();

            $formattedOrders = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'item_count' => $order->items()->count(),
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Orders retrieved successfully',
                'data' => $formattedOrders
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific order with items
     */
    public function show(Request $request, $orderId)
    {
        try {
            $user = $request->user();
            $order = Order::where('user_id', $user->id)->with('items.product')->find($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $order->id,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    'transaction_id' => $order->transaction_id,
                    'razorpay_order_id' => $order->razorpay_order_id,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'product' => [
                                'id' => $item->product->id,
                                'name' => $item->product->name,
                                'image' => $item->product->image,
                            ],
                            'quantity' => $item->quantity,
                            'price_at_purchase' => $item->price,
                            'subtotal' => $item->quantity * $item->price,
                        ];
                    }),
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Checkout cart and create order
     */
    public function checkout(Request $request)
    {
        try {
            $order = $this->orderService->createFromCart($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'id' => $order->id,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Checkout failed: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Update order status (admin only)
     */
    public function updateStatus(Request $request, $orderId)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
                'payment_status' => 'sometimes|in:pending,paid,failed,refunded',
            ]);

            $order = Order::findOrFail($orderId);
            $this->orderService->updateStatus($order, $validated['status'], $validated['payment_status'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated',
                'data' => [
                    'id' => $order->id,
                    'status' => $order->status,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel order and restore stock
     */
    public function cancel(Request $request, $orderId)
    {
        try {
            $order = Order::where('user_id', $request->user()->id)->findOrFail($orderId);
            $this->orderService->cancelOrder($order);

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully and stock restored',
                'data' => [
                    'id' => $order->id,
                    'status' => $order->status,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * DEPRECATED: Simulated payment (Frontend should use Razorpay)
     */
    public function pay(Request $request, $orderId)
    {
        // For backwards compatibility or simplified testing
        try {
            $order = Order::where('user_id', $request->user()->id)->findOrFail($orderId);
            $this->orderService->updateStatus($order, 'processing', 'paid');
            $order->update(['payment_method' => $request->method ?? 'simulated', 'transaction_id' => 'sim_' . uniqid()]);
            
            return response()->json(['success' => true, 'message' => 'Simulated payment successful']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}

