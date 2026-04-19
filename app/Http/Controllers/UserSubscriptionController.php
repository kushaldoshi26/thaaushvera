<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSubscriptionController extends Controller
{
    /**
     * Get all available subscription plans (public)
     */
    public function plans()
    {
        $plans = Subscription::where('active', true)->orderBy('price')->get();
        return response()->json(['success' => true, 'data' => $plans]);
    }

    /**
     * Get logged-in user's current subscription
     */
    public function mySubscription(Request $request)
    {
        $user = $request->user();
        $active = UserSubscription::with('plan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->latest()
            ->first();

        if (!$active) {
            return response()->json(['success' => true, 'subscription' => null, 'has_subscription' => false]);
        }

        return response()->json([
            'success'          => true,
            'has_subscription' => true,
            'subscription'     => [
                'id'          => $active->id,
                'plan_id'     => $active->subscription_id,
                'plan_name'   => $active->plan->name ?? 'Unknown',
                'plan_price'  => $active->plan->price ?? 0,
                'description' => $active->plan->description ?? '',
                'starts_at'   => $active->starts_at?->format('M d, Y'),
                'ends_at'     => $active->ends_at?->format('M d, Y') ?? 'Lifetime',
                'status'      => $active->status,
                'amount_paid' => $active->amount_paid,
            ]
        ]);
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        $user = $request->user();
        $plan = Subscription::findOrFail($request->subscription_id);

        // Cancel any existing active subscription
        UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        // Calculate end date
        $endsAt = now()->addMonths($plan->duration_months);

        $sub = UserSubscription::create([
            'user_id'         => $user->id,
            'subscription_id' => $plan->id,
            'starts_at'       => now(),
            'ends_at'         => $endsAt,
            'status'          => 'active',
            'payment_method'  => $request->payment_method ?? 'manual',
            'amount_paid'     => $plan->price,
        ]);

        return response()->json([
            'success'      => true,
            'message'      => "You are now subscribed to {$plan->name}!",
            'subscription' => [
                'plan_name' => $plan->name,
                'ends_at'   => $endsAt->format('M d, Y'),
                'status'    => 'active',
            ]
        ], 201);
    }

    /**
     * Cancel current subscription
     */
    public function cancel(Request $request)
    {
        $user = $request->user();
        $updated = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        if ($updated === 0) {
            return response()->json(['success' => false, 'message' => 'No active subscription found'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Subscription cancelled successfully']);
    }

    /**
     * Get all user subscriptions (history)
     */
    public function history(Request $request)
    {
        $subs = UserSubscription::with('plan')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn($s) => [
                'id'        => $s->id,
                'plan'      => $s->plan->name ?? 'Unknown',
                'status'    => $s->status,
                'starts_at' => $s->starts_at?->format('M d, Y'),
                'ends_at'   => $s->ends_at?->format('M d, Y') ?? 'Lifetime',
                'paid'      => $s->amount_paid,
            ]);

        return response()->json(['success' => true, 'data' => $subs]);
    }
}
