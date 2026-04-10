@extends('layouts.admin')
@section('title', 'Coupons')
@section('page-title', 'Coupon Management')

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
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="couponsTable">
                <tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--admin-muted);">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="couponModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <h3 class="modal-title">Add Coupon</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="couponForm">
            <div class="modal-body">
                <div class="admin-form-group">
                    <label>Coupon Code <span style="color:#ef4444">*</span></label>
                    <input type="text" id="couponCode" class="admin-input" required placeholder="e.g. WELCOME20">
                </div>
                <div class="grid-2">
                    <div class="admin-form-group">
                        <label>Discount Value <span style="color:#ef4444">*</span></label>
                        <input type="number" id="couponDiscount" class="admin-input" required placeholder="0">
                    </div>
                    <div class="admin-form-group">
                        <label>Type</label>
                        <select id="couponType" class="admin-select admin-input">
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>
                </div>
                <div class="admin-form-group">
                    <label>Expiry Date</label>
                    <input type="date" id="couponExpiry" class="admin-input">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Coupon</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function loadCoupons() {
    try {
        const token = api.getToken();
        const response = await fetch('{{ url("/api/admin/coupons") }}', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();
        const coupons = data.coupons || data.data || [];
        const tbody = document.getElementById('couponsTable');
        
        if (coupons.length > 0) {
            tbody.innerHTML = coupons.map(c => `
                <tr class="cpn-row" data-code="${c.code.toLowerCase()}">
                    <td><strong>${c.code}</strong></td>
                    <td>${c.value}${c.type === 'percentage' ? '%' : '₹'}</td>
                    <td class="text-muted" style="text-transform:capitalize;">${c.type}</td>
                    <td class="text-muted text-sm">${c.valid_until ? new Date(c.valid_until).toLocaleDateString() : 'No expiry'}</td>
                    <td><span class="badge badge-${c.is_active ? 'active' : 'cancelled'}">${c.is_active ? 'Active' : 'Inactive'}</span></td>
                    <td>
                        <div class="flex gap-1">
                            <button class="btn btn-outline btn-sm" onclick="toggleCoupon(${c.id}, ${c.is_active})">${c.is_active ? 'Deactivate' : 'Activate'}</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteCoupon(${c.id})">Delete</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--admin-muted);">No coupons found</td></tr>';
        }
    } catch (error) {
        console.error('Error loading coupons:', error);
        document.getElementById('couponsTable').innerHTML = '<tr><td colspan="6" class="text-center">No coupons found</td></tr>';
    }
}

function filterTable(q) {
    const rows = document.querySelectorAll('.cpn-row');
    rows.forEach(r => r.style.display = r.dataset.code.includes(q.toLowerCase()) ? '' : 'none');
}

function openAddModal() {
    document.getElementById('couponModal').classList.add('open');
}

function closeModal() {
    document.getElementById('couponModal').classList.remove('open');
}

document.getElementById('couponForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = {
        code: document.getElementById('couponCode').value,
        type: document.getElementById('couponType').value,
        value: document.getElementById('couponDiscount').value,
        valid_until: document.getElementById('couponExpiry').value || null,
        is_active: true
    };
    
    try {
        const token = api.getToken();
        const response = await fetch('{{ url("/api/admin/coupons") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            alert('Coupon added successfully');
            closeModal();
            loadCoupons();
        } else {
            alert('Failed to add coupon');
        }
    } catch (error) {
        alert('Error adding coupon');
    }
});

async function deleteCoupon(id) {
    if (!confirm('Delete this coupon?')) return;
    
    try {
        const token = api.getToken();
        await fetch(`{{ url("/api/admin/coupons") }}/${id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${token}` }
        });
        alert('Coupon deleted');
        loadCoupons();
    } catch (error) {
        alert('Error deleting coupon');
    }
}

async function toggleCoupon(id, currentStatus) {
    try {
        const token = api.getToken();
        await fetch(`{{ url("/api/admin/coupons") }}/${id}/toggle`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({ is_active: !currentStatus })
        });
        alert('Coupon status updated');
        loadCoupons();
    } catch (error) {
        alert('Error updating coupon');
    }
}

loadCoupons();
</script>
@endpush
