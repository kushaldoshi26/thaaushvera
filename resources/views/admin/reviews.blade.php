@extends('admin.template')

@section('admin-content')
<div class="admin-header">
    <div class="header-left">
        <h1>Product Reviews</h1>
        <p class="subtitle">Moderate and manage customer feedback</p>
    </div>
    <div class="header-right">
        <select class="admin-select" id="statusFilter" onchange="loadReviews()">
            <option value="">All Reviews</option>
            <option value="pending" selected>Pending Moderation</option>
            <option value="approved">Approved</option>
        </select>
    </div>
</div>

<div class="admin-card">
    <div class="table-container">
        <table class="admin-table" id="reviewsTable">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Product</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="reviewsList">
                <!-- Reviews will be loaded dynamically -->
            </tbody>
        </table>
    </div>
    <div id="pagination" class="pagination-container"></div>
</div>

@push('scripts')
<script>
let currentPage = 1;

async function loadReviews() {
    const status = document.getElementById('statusFilter').value;
    try {
        const response = await fetch(`${baseUrl}/admin/reviews?status=${status}&page=${currentPage}`, {
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        renderReviews(data.data);
        renderPagination(data);
    } catch (error) {
        console.error('Error loading reviews:', error);
    }
}

function renderReviews(reviews) {
    const list = document.getElementById('reviewsList');
    list.innerHTML = reviews.map(review => `
        <tr>
            <td>
                <div class="tbl-user">
                    <div class="tbl-user-name">${review.user.name}</div>
                    <div class="tbl-user-email">${review.user.email}</div>
                </div>
            </td>
            <td>${review.product.name}</td>
            <td>
                <div class="tbl-rating">${'★'.repeat(review.rating)}${'☆'.repeat(5-review.rating)}</div>
            </td>
            <td>
                <div class="tbl-review-text" title="${review.review_text}">
                    ${review.review_text.substring(0, 50)}${review.review_text.length > 50 ? '...' : ''}
                </div>
                ${review.is_verified_purchase ? '<span class="verified-badge">Verified</span>' : ''}
            </td>
            <td>
                <span class="status-pill ${review.is_approved ? 'status-delivered' : 'status-pending'}">
                    ${review.is_approved ? 'Approved' : 'Pending'}
                </span>
            </td>
            <td>${new Date(review.created_at).toLocaleDateString()}</td>
            <td>
                <div class="tbl-actions">
                    ${!review.is_approved ? 
                        `<button class="btn-icon btn-approve" onclick="updateStatus(${review.id}, true)" title="Approve">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>` : 
                        `<button class="btn-icon btn-reject" onclick="updateStatus(${review.id}, false)" title="Reject/Unapprove">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>`
                    }
                    <button class="btn-icon btn-delete" onclick="deleteReview(${review.id})" title="Delete">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

async function updateStatus(id, isApproved) {
    if (!confirm(`Are you sure you want to ${isApproved ? 'approve' : 'reject'} this review?`)) return;
    
    try {
        const response = await fetch(`${baseUrl}/admin/reviews/${id}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${adminToken}`,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ is_approved: isApproved })
        });
        if (response.ok) {
            loadReviews();
        }
    } catch (error) {
        console.error('Error updating status:', error);
    }
}

async function deleteReview(id) {
    if (!confirm('Are you sure you want to permanently delete this review?')) return;
    
    try {
        const response = await fetch(`${baseUrl}/admin/reviews/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Accept': 'application/json'
            }
        });
        if (response.ok) {
            loadReviews();
        }
    } catch (error) {
        console.error('Error deleting review:', error);
    }
}

function renderPagination(data) {
    // Basic pagination logic
}

loadReviews();
</script>

<style>
.tbl-rating { color: var(--gold); }
.verified-badge {
    font-size: 0.7rem;
    color: #2e7d32;
    background: #e8f5e9;
    padding: 2px 6px;
    border-radius: 4px;
    margin-left: 5px;
}
.btn-approve { color: #2e7d32; }
.btn-reject { color: #c62828; }
.tbl-review-text { max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>
@endpush
@endsection
