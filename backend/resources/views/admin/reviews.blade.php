@extends('layouts.admin')
@section('title', 'Reviews')

@section('content')
<div class="page-bar">
    <div class="page-bar-title">Reviews</div>
    <div class="search-bar">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search reviews..." oninput="filterReviews(this.value)">
    </div>
    <select class="admin-input admin-select" id="reviewFilter" onchange="filterByApproval(this.value)" style="width:auto;">
        <option value="">All Reviews</option>
        <option value="1">Approved</option>
        <option value="0">Pending</option>
    </select>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(3,1fr); margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon amber">⭐</div>
        <div><div class="stat-label">Total Reviews</div><div class="stat-value">{{ $reviews->count() }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div><div class="stat-label">Approved</div><div class="stat-value">{{ $reviews->where('is_approved', true)->count() }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">⏳</div>
        <div><div class="stat-label">Pending</div><div class="stat-value">{{ $reviews->where('is_approved', false)->count() }}</div></div>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                <tr class="review-row" 
                    data-search="{{ strtolower(($review->user->name ?? '') . ' ' . ($review->product->name ?? '') . ' ' . $review->review_text) }}"
                    data-approved="{{ $review->is_approved ? '1' : '0' }}">
                    <td class="text-muted text-sm">{{ $review->id }}</td>
                    <td><strong>{{ $review->user->name ?? 'Anonymous' }}</strong></td>
                    <td class="text-muted">{{ Str::limit($review->product->name ?? '—', 25) }}</td>
                    <td>
                        <span style="color:var(--admin-gold); letter-spacing:1px;">
                            {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                        </span>
                    </td>
                    <td style="max-width:220px;">{{ Str::limit($review->review_text, 60) }}</td>
                    <td class="text-muted text-sm">{{ $review->created_at->format('M d, Y') }}</td>
                    <td>
                        <span class="badge {{ $review->is_approved ? 'badge-active' : 'badge-pending' }}" id="badge-{{ $review->id }}">
                            {{ $review->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    </td>
                    <td>
                        <div class="flex gap-1">
                            @if(!$review->is_approved)
                            <button class="btn btn-outline btn-sm" onclick="updateReview({{ $review->id }}, 'approved')">Approve</button>
                            @else
                            <button class="btn btn-outline btn-sm" onclick="updateReview({{ $review->id }}, 'rejected')">Reject</button>
                            @endif
                            <button class="btn btn-danger btn-sm" onclick="deleteReview({{ $review->id }})">Delete</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center; padding:2.5rem; color:var(--admin-muted);">No reviews yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function filterReviews(q) {
    document.querySelectorAll('.review-row').forEach(r => {
        r.style.display = r.dataset.search.includes(q.toLowerCase()) ? '' : 'none';
    });
}
function filterByApproval(v) {
    document.querySelectorAll('.review-row').forEach(r => {
        r.style.display = (!v || r.dataset.approved === v) ? '' : 'none';
    });
}

async function updateReview(id, status) {
    const token = localStorage.getItem('auth_token');
    try {
        const res = await fetch(`/api/admin/reviews/${id}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, ...(token ? { Authorization: 'Bearer ' + token } : {}) },
            body: JSON.stringify({ status })
        });
        if (res.ok) window.location.reload();
        else { const e = await res.json(); alert(e.message || 'Failed'); }
    } catch { alert('Network error'); }
}

async function deleteReview(id) {
    if (!confirm('Delete this review?')) return;
    const token = localStorage.getItem('auth_token');
    try {
        const res = await fetch(`/api/admin/reviews/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, ...(token ? { Authorization: 'Bearer ' + token } : {}) }
        });
        if (res.ok) window.location.reload();
        else { const e = await res.json(); alert(e.message || 'Failed'); }
    } catch { alert('Network error'); }
}
</script>
@endpush
