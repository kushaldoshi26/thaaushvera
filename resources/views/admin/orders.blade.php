@extends('layouts.admin')
@section('title', 'Orders')

@section('content')
<div class="page-bar">
    <div class="page-bar-title">Orders</div>
    <div class="search-bar">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search orders..." oninput="filterOrders(this.value)">
    </div>
    <select class="admin-input admin-select" id="statusFilter" onchange="filterByStatus(this.value)" style="width:auto;">
        <option value="">All Statuses</option>
        <option value="pending">Pending</option>
        <option value="processing">Processing</option>
        <option value="shipped">Shipped</option>
        <option value="delivered">Delivered</option>
        <option value="cancelled">Cancelled</option>
    </select>
</div>

{{-- Stats --}}
<div class="stats-grid" style="grid-template-columns:repeat(5,1fr); margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon amber">📋</div>
        <div><div class="stat-label">Total</div><div class="stat-value">{{ $orders->count() }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">⏳</div>
        <div><div class="stat-label">Pending</div><div class="stat-value">{{ $orders->where('status','pending')->count() }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">⚙️</div>
        <div><div class="stat-label">Processing</div><div class="stat-value">{{ $orders->where('status','processing')->count() }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div><div class="stat-label">Delivered</div><div class="stat-value">{{ $orders->where('status','delivered')->count() }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">💰</div>
        <div><div class="stat-label">Revenue</div><div class="stat-value" style="font-size:1.1rem;">₹{{ number_format($orders->sum('total_amount'), 0) }}</div></div>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="ordersBody">
                @forelse($orders as $order)
                <tr class="order-row" data-name="{{ strtolower($order->user->name ?? '') }}" data-status="{{ $order->status }}">
                    <td><strong>#{{ $order->id }}</strong></td>
                    <td>
                        <div><strong>{{ $order->user->name ?? 'Guest' }}</strong></div>
                        <div class="text-sm text-muted">{{ $order->user->email ?? '—' }}</div>
                    </td>
                    <td class="text-muted text-sm">{{ $order->created_at->format('M d, Y') }}<br>{{ $order->created_at->format('H:i') }}</td>
                    <td><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
                    <td>
                        <span class="badge {{ $order->payment_status === 'paid' ? 'badge-paid' : 'badge-pending' }}">
                            {{ ucfirst($order->payment_status ?? 'pending') }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                    </td>
                    <td>
                        <select class="admin-input admin-select" style="width:auto; padding:4px 8px; font-size:0.8rem;"
                                onchange="updateOrderStatus({{ $order->id }}, this.value)">
                            <option value="pending"     {{ $order->status === 'pending'     ? 'selected' : '' }}>Pending</option>
                            <option value="processing"  {{ $order->status === 'processing'  ? 'selected' : '' }}>Processing</option>
                            <option value="shipped"     {{ $order->status === 'shipped'     ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered"   {{ $order->status === 'delivered'   ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled"   {{ $order->status === 'cancelled'   ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center; padding:2.5rem; color:var(--admin-muted);">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function filterOrders(q) {
    document.querySelectorAll('.order-row').forEach(r => {
        r.style.display = r.dataset.name.includes(q.toLowerCase()) ? '' : 'none';
    });
}

function filterByStatus(status) {
    document.querySelectorAll('.order-row').forEach(r => {
        r.style.display = (!status || r.dataset.status === status) ? '' : 'none';
    });
}

async function updateOrderStatus(id, status) {
    try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`/api/admin/orders/${id}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, ...(token ? { Authorization: 'Bearer ' + token } : {}) },
            body: JSON.stringify({ status })
        });
        const data = await res.json();
        if (!res.ok) alert(data.message || 'Failed to update status');
        // Update the badge in the row
        const row = document.querySelector(`.order-row td select[onchange*="${id}"]`).closest('tr');
        const badge = row.querySelector('.badge-pending, .badge-processing, .badge-shipped, .badge-delivered, .badge-cancelled');
        if (badge) {
            badge.className = `badge badge-${status}`;
            badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        }
        row.dataset.status = status;
    } catch { alert('Network error updating order'); }
}
</script>
@endpush
