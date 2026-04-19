@extends('layouts.admin')
@section('title', 'Coupons')

@section('content')
<div class="page-bar">
    <div class="page-bar-title">Coupons</div>
    <div class="search-bar">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="couponSearch" placeholder="Search coupons..." oninput="filterTable(this.value)">
    </div>
    <button class="btn btn-primary" onclick="openAddModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Coupon
    </button>
</div>

<div class="card">
    <div class="table-wrap">
        <table id="couponsTableMain">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Type</th>
                    <th>Expires</th>
                    <th>Uses</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="couponsTable">
                <tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--admin-muted);">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="couponModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <h3 class="modal-title" id="couponModalTitle">Add Coupon</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="couponForm">
            <input type="hidden" id="editCouponId">
            <div class="modal-body">
                <div class="admin-form-group">
                    <label>Coupon Code <span style="color:#ef4444">*</span></label>
                    <input type="text" id="couponCode" class="admin-input" required placeholder="e.g. WELCOME20" style="text-transform:uppercase;">
                </div>
                <div class="grid-2">
                    <div class="admin-form-group">
                        <label>Discount Value <span style="color:#ef4444">*</span></label>
                        <input type="number" id="couponDiscount" class="admin-input" required placeholder="0" min="0" step="0.01">
                    </div>
                    <div class="admin-form-group">
                        <label>Type</label>
                        <select id="couponType" class="admin-select admin-input">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₹)</option>
                        </select>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="admin-form-group">
                        <label>Min Order Amount (₹)</label>
                        <input type="number" id="couponMinOrder" class="admin-input" placeholder="0" min="0">
                    </div>
                    <div class="admin-form-group">
                        <label>Usage Limit</label>
                        <input type="number" id="couponUsageLimit" class="admin-input" placeholder="Unlimited" min="1">
                    </div>
                </div>
                <div class="admin-form-group">
                    <label>Expiry Date</label>
                    <input type="date" id="couponExpiry" class="admin-input">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="couponSaveBtn">Save Coupon</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function getAdminToken() {
    return localStorage.getItem('auth_token') || '';
}

function adminHeaders(contentType = false) {
    const h = {
        'Accept': 'application/json',
        'Authorization': `Bearer ${getAdminToken()}`,
        'X-CSRF-TOKEN': CSRF
    };
    if (contentType) h['Content-Type'] = 'application/json';
    return h;
}

async function loadCoupons() {
    try {
        const res = await fetch('/api/admin/coupons', { headers: adminHeaders() });
        const json = await res.json();
        const coupons = json.data || json.coupons || [];
        const tbody = document.getElementById('couponsTable');

        if (!res.ok) {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:2rem;color:#ef4444;">Error: ${json.message || 'Failed to load'}</td></tr>`;
            return;
        }

        if (coupons.length > 0) {
            tbody.innerHTML = coupons.map(c => `
                <tr class="cpn-row" data-code="${(c.code||'').toLowerCase()}">
                    <td><strong>${c.code}</strong></td>
                    <td><strong>${c.value}${c.type === 'percentage' ? '%' : ' ₹'}</strong></td>
                    <td class="text-muted" style="text-transform:capitalize;">${c.type}</td>
                    <td class="text-muted text-sm">${c.valid_until ? new Date(c.valid_until).toLocaleDateString('en-IN') : '<em>No expiry</em>'}</td>
                    <td class="text-muted text-sm">${c.used_count ?? 0}${c.usage_limit ? ' / ' + c.usage_limit : ''}</td>
                    <td><span class="badge badge-${c.is_active ? 'active' : 'cancelled'}">${c.is_active ? 'Active' : 'Inactive'}</span></td>
                    <td>
                        <div class="flex gap-1">
                            <button class="btn btn-outline btn-sm" onclick="toggleCoupon(${c.id}, ${c.is_active ? 'true' : 'false'})">${c.is_active ? 'Deactivate' : 'Activate'}</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteCoupon(${c.id})">Delete</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--admin-muted);">No coupons found. Add your first coupon!</td></tr>';
        }
    } catch (err) {
        console.error('Error loading coupons:', err);
        document.getElementById('couponsTable').innerHTML = '<tr><td colspan="7" class="text-center" style="padding:2rem;color:#ef4444;">Error loading coupons</td></tr>';
    }
}

function filterTable(q) {
    document.querySelectorAll('.cpn-row').forEach(r => {
        r.style.display = r.dataset.code.includes(q.toLowerCase()) ? '' : 'none';
    });
}

function openAddModal() {
    document.getElementById('couponModalTitle').textContent = 'Add Coupon';
    document.getElementById('couponForm').reset();
    document.getElementById('editCouponId').value = '';
    document.getElementById('couponSaveBtn').textContent = 'Save Coupon';
    document.getElementById('couponModal').classList.add('open');
}

function closeModal() {
    document.getElementById('couponModal').classList.remove('open');
}

document.getElementById('couponForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('editCouponId').value;
    const btn = document.getElementById('couponSaveBtn');
    btn.disabled = true; btn.textContent = 'Saving...';

    const payload = {
        code:              document.getElementById('couponCode').value.toUpperCase(),
        type:              document.getElementById('couponType').value,
        value:             parseFloat(document.getElementById('couponDiscount').value),
        valid_until:       document.getElementById('couponExpiry').value || null,
        is_active:         true
    };
    const minOrder = document.getElementById('couponMinOrder').value;
    if (minOrder) payload.min_order_amount = parseFloat(minOrder);
    const usageLimit = document.getElementById('couponUsageLimit').value;
    if (usageLimit) payload.usage_limit = parseInt(usageLimit);

    try {
        const url = id ? `/api/admin/coupons/${id}` : '/api/admin/coupons';
        const method = id ? 'PUT' : 'POST';
        const res = await fetch(url, {
            method,
            headers: adminHeaders(true),
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (res.ok) {
            alert(id ? 'Coupon updated!' : 'Coupon created!');
            closeModal();
            loadCoupons();
        } else {
            const errMsg = json.message || (json.errors ? Object.values(json.errors).flat().join(', ') : 'Failed');
            alert('Error: ' + errMsg);
        }
    } catch (err) {
        alert('Network error');
    } finally {
        btn.disabled = false; btn.textContent = 'Save Coupon';
    }
});

async function deleteCoupon(id) {
    if (!confirm('Delete this coupon? This cannot be undone.')) return;
    try {
        const res = await fetch(`/api/admin/coupons/${id}`, {
            method: 'DELETE',
            headers: adminHeaders()
        });
        if (res.ok) { alert('Coupon deleted'); loadCoupons(); }
        else { const j = await res.json(); alert('Failed: ' + (j.message || 'Error')); }
    } catch { alert('Network error'); }
}

async function toggleCoupon(id, currentStatus) {
    try {
        const res = await fetch(`/api/admin/coupons/${id}/toggle`, {
            method: 'PUT',
            headers: adminHeaders(true),
            body: JSON.stringify({ is_active: !currentStatus })
        });
        if (res.ok) { loadCoupons(); }
        else { const j = await res.json(); alert('Failed: ' + (j.message || 'Error')); }
    } catch { alert('Network error'); }
}

loadCoupons();
</script>
@endpush
