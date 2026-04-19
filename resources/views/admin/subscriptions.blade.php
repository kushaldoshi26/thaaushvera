@extends('layouts.admin')
@section('title', 'Subscriptions')

@section('content')
<div class="page-bar">
    <div class="page-bar-title">Subscription Management</div>
    <button class="btn btn-primary" onclick="openPlanModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Plan
    </button>
</div>

{{-- Tab Navigation --}}
<div style="display:flex;gap:0;margin-bottom:1.5rem;border-bottom:1px solid rgba(255,255,255,0.07);">
    <button class="sub-tab active" id="tabPlans" onclick="switchSubTab('plans')">📋 Subscription Plans</button>
    <button class="sub-tab" id="tabUsers" onclick="switchSubTab('users')">👥 User Subscriptions</button>
    <button class="sub-tab" id="tabExpired" onclick="switchSubTab('expired')">⏰ Expired / Cancelled</button>
</div>

{{-- ── Plans Tab ───────────────────────────── --}}
<div id="panelPlans">
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Plan Name</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Description</th>
                        <th>Active</th>
                        <th>Subscribers</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="plansTable">
                    <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--admin-muted);">Loading plans...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Active User Subscriptions Tab ─────── --}}
<div id="panelUsers" style="display:none;">
    <div style="display:flex;gap:1rem;margin-bottom:1rem;">
        <div class="stat-card" style="flex:1;"><div class="stat-icon green">✅</div><div><div class="stat-label">Active</div><div class="stat-value" id="activeCount">—</div></div></div>
        <div class="stat-card" style="flex:1;"><div class="stat-icon amber">💰</div><div><div class="stat-label">Revenue</div><div class="stat-value" id="totalRevenue">—</div></div></div>
        <div class="stat-card" style="flex:1;"><div class="stat-icon purple">📅</div><div><div class="stat-label">Expiring Soon</div><div class="stat-value" id="expiringSoon">—</div></div></div>
    </div>
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Plan</th>
                        <th>Started</th>
                        <th>Expires</th>
                        <th>Amount Paid</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="userSubsTable">
                    <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--admin-muted);">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Expired Tab ─────────────────────────── --}}
<div id="panelExpired" style="display:none;">
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Plan</th>
                        <th>Started</th>
                        <th>Ended</th>
                        <th>Status</th>
                        <th>Paid</th>
                    </tr>
                </thead>
                <tbody id="expiredTable">
                    <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--admin-muted);">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Plan Modal ───────────────────────────── --}}
<div class="modal-overlay" id="planModal">
    <div class="modal" style="max-width:480px;">
        <div class="modal-header">
            <h3 class="modal-title" id="planModalTitle">Add Subscription Plan</h3>
            <button class="modal-close" onclick="closePlanModal()">×</button>
        </div>
        <form id="planForm">
            <input type="hidden" id="planId">
            <div class="modal-body">
                <div class="admin-form-group">
                    <label>Plan Name <span style="color:#ef4444">*</span></label>
                    <input type="text" id="planName" class="admin-input" required placeholder="e.g. Gold Wellness Plan">
                </div>
                <div class="grid-2">
                    <div class="admin-form-group">
                        <label>Price (₹) <span style="color:#ef4444">*</span></label>
                        <input type="number" id="planPrice" class="admin-input" required placeholder="999" min="0" step="0.01">
                    </div>
                    <div class="admin-form-group">
                        <label>Duration (months) <span style="color:#ef4444">*</span></label>
                        <input type="number" id="planDuration" class="admin-input" required placeholder="3" min="1" value="1">
                    </div>
                </div>
                <div class="admin-form-group">
                    <label>Description</label>
                    <textarea id="planDescription" class="admin-input admin-textarea" rows="3" placeholder="What does this plan include?"></textarea>
                </div>
                <div class="admin-form-group" style="display:flex;align-items:center;gap:10px;">
                    <input type="checkbox" id="planActive" checked style="width:18px;height:18px;cursor:pointer;">
                    <label for="planActive" style="margin:0;cursor:pointer;">Plan is Active (visible to users)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closePlanModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="planSaveBtn">Save Plan</button>
            </div>
        </form>
    </div>
</div>

<style>
.sub-tab {
    background: none;
    border: none;
    color: var(--admin-muted, #9ca3af);
    padding: 10px 20px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
    font-family: inherit;
}
.sub-tab.active, .sub-tab:hover {
    color: var(--admin-gold, #c9a96e);
    border-bottom-color: var(--admin-gold, #c9a96e);
}
</style>
@endsection

@push('scripts')
<script>
const CSRF_T = document.querySelector('meta[name="csrf-token"]').content;

function getToken() { return localStorage.getItem('auth_token') || ''; }
function hdr(json = false) {
    const h = { 'Accept': 'application/json', 'Authorization': 'Bearer ' + getToken(), 'X-CSRF-TOKEN': CSRF_T };
    if (json) h['Content-Type'] = 'application/json';
    return h;
}

// ── Tab Switching ─────────────────────────────────────
function switchSubTab(tab) {
    document.querySelectorAll('.sub-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tabPlans').classList.remove('active');
    document.getElementById('tabUsers').classList.remove('active');
    document.getElementById('tabExpired').classList.remove('active');
    document.getElementById('panelPlans').style.display = 'none';
    document.getElementById('panelUsers').style.display = 'none';
    document.getElementById('panelExpired').style.display = 'none';

    document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.add('active');
    document.getElementById('panel' + tab.charAt(0).toUpperCase() + tab.slice(1)).style.display = '';

    if (tab === 'plans') loadPlans();
    if (tab === 'users') loadUserSubs('active');
    if (tab === 'expired') loadUserSubs('expired');
}

// ── Load Subscription Plans ───────────────────────────
async function loadPlans() {
    const tbody = document.getElementById('plansTable');
    try {
        const res = await fetch('/api/admin/subscriptions', { headers: hdr() });
        const json = await res.json();
        const plans = json.data || json || [];

        if (!plans.length) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--admin-muted);">No plans yet. Add your first subscription plan!</td></tr>';
            return;
        }
        tbody.innerHTML = plans.map(p => `
            <tr>
                <td class="text-muted text-sm">${p.id}</td>
                <td><strong>${p.name}</strong></td>
                <td><strong>₹${parseFloat(p.price).toFixed(2)}</strong></td>
                <td>${p.duration_months} month${p.duration_months > 1 ? 's' : ''}</td>
                <td class="text-muted text-sm" style="max-width:200px;white-space:normal;">${p.description || '—'}</td>
                <td><span class="badge badge-${p.active ? 'active' : 'cancelled'}">${p.active ? 'Active' : 'Inactive'}</span></td>
                <td class="text-muted">${p.subscribers_count ?? '—'}</td>
                <td>
                    <div class="flex gap-1">
                        <button class="btn btn-outline btn-sm" onclick="editPlan(${p.id}, '${(p.name||'').replace(/'/g,"\\'")}', ${p.price}, ${p.duration_months}, '${(p.description||'').replace(/'/g,"\\'")}', ${p.active ? 1 : 0})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deletePlan(${p.id})">Delete</button>
                    </div>
                </td>
            </tr>
        `).join('');
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#ef4444;">Error loading plans</td></tr>';
    }
}

// ── Load User Subscriptions ───────────────────────────
async function loadUserSubs(statusFilter) {
    const isExpired = statusFilter === 'expired';
    const tbody = document.getElementById(isExpired ? 'expiredTable' : 'userSubsTable');

    try {
        const res = await fetch('/api/admin/user-subscriptions?status=' + statusFilter, { headers: hdr() });
        const json = await res.json();
        const subs = json.data || [];

        if (!isExpired) {
            const total = subs.reduce((a, s) => a + parseFloat(s.amount_paid || 0), 0);
            const expiring = subs.filter(s => {
                if (!s.ends_at) return false;
                const d = new Date(s.ends_at_raw || s.ends_at);
                return (d - new Date()) < 7 * 24 * 60 * 60 * 1000;
            }).length;
            document.getElementById('activeCount').textContent = subs.length;
            document.getElementById('totalRevenue').textContent = '₹' + total.toFixed(2);
            document.getElementById('expiringSoon').textContent = expiring;
        }

        if (!subs.length) {
            tbody.innerHTML = `<tr><td colspan="${isExpired ? 7 : 8}" style="text-align:center;padding:2rem;color:var(--admin-muted);">${isExpired ? 'No expired or cancelled subscriptions.' : 'No active user subscriptions yet.'}</td></tr>`;
            return;
        }

        tbody.innerHTML = subs.map(s => `
            <tr>
                <td class="text-muted text-sm">${s.id}</td>
                <td>
                    <div style="font-weight:600;">${s.user_name || '—'}</div>
                    <div class="text-muted text-sm">${s.user_email || ''}</div>
                </td>
                <td><span class="badge badge-active" style="background:rgba(184,150,76,0.15);color:#B8964C;border:1px solid rgba(184,150,76,0.3);">${s.plan_name || '—'}</span></td>
                <td class="text-muted text-sm">${s.starts_at || '—'}</td>
                <td class="text-muted text-sm">${s.ends_at || 'Lifetime'}</td>
                ${!isExpired ? `<td class="text-muted">₹${parseFloat(s.amount_paid || 0).toFixed(2)}</td>` : ''}
                <td><span class="badge badge-${s.status === 'active' ? 'active' : 'cancelled'}">${s.status}</span></td>
                ${!isExpired ? `
                <td>
                    <div class="flex gap-1">
                        <button class="btn btn-outline btn-sm" onclick="extendSub(${s.id})">Extend</button>
                        <button class="btn btn-danger btn-sm" onclick="cancelUserSub(${s.id})">Cancel</button>
                    </div>
                </td>` : `<td class="text-muted text-sm">₹${parseFloat(s.amount_paid || 0).toFixed(2)}</td>`}
            </tr>
        `).join('');
    } catch(e) {
        tbody.innerHTML = `<tr><td colspan="${isExpired ? 7 : 8}" style="text-align:center;color:#ef4444;">Error loading subscriptions</td></tr>`;
    }
}

// ── Plan CRUD ─────────────────────────────────────────
function openPlanModal() {
    document.getElementById('planModalTitle').textContent = 'Add Subscription Plan';
    document.getElementById('planForm').reset();
    document.getElementById('planId').value = '';
    document.getElementById('planActive').checked = true;
    document.getElementById('planModal').classList.add('open');
}

function editPlan(id, name, price, duration, description, active) {
    document.getElementById('planModalTitle').textContent = 'Edit Plan';
    document.getElementById('planId').value = id;
    document.getElementById('planName').value = name;
    document.getElementById('planPrice').value = price;
    document.getElementById('planDuration').value = duration;
    document.getElementById('planDescription').value = description;
    document.getElementById('planActive').checked = !!active;
    document.getElementById('planModal').classList.add('open');
}

function closePlanModal() {
    document.getElementById('planModal').classList.remove('open');
}

document.getElementById('planForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('planSaveBtn');
    btn.disabled = true; btn.textContent = 'Saving...';
    const id = document.getElementById('planId').value;
    const payload = {
        name:             document.getElementById('planName').value,
        price:            parseFloat(document.getElementById('planPrice').value),
        duration_months:  parseInt(document.getElementById('planDuration').value),
        description:      document.getElementById('planDescription').value || null,
        active:           document.getElementById('planActive').checked ? 1 : 0,
    };
    try {
        const url    = id ? `/api/admin/subscriptions/${id}` : '/api/admin/subscriptions';
        const method = id ? 'PUT' : 'POST';
        const res    = await fetch(url, { method, headers: hdr(true), body: JSON.stringify(payload) });
        const json   = await res.json();
        if (res.ok) {
            alert(id ? 'Plan updated!' : 'Plan created!');
            closePlanModal();
            loadPlans();
        } else {
            alert('Error: ' + (json.message || JSON.stringify(json.errors || {})));
        }
    } catch { alert('Network error'); }
    finally { btn.disabled = false; btn.textContent = 'Save Plan'; }
});

async function deletePlan(id) {
    if (!confirm('Delete this subscription plan? Existing user subscriptions will NOT be affected.')) return;
    const res = await fetch(`/api/admin/subscriptions/${id}`, { method: 'DELETE', headers: hdr() });
    if (res.ok) { alert('Plan deleted'); loadPlans(); }
    else { const j = await res.json(); alert('Failed: ' + (j.message || 'Error')); }
}

// ── User Subscription Actions ─────────────────────────
async function cancelUserSub(id) {
    if (!confirm('Cancel this user\'s subscription?')) return;
    const res = await fetch(`/api/admin/user-subscriptions/${id}/cancel`, { method: 'POST', headers: hdr(true) });
    if (res.ok) { alert('Subscription cancelled'); loadUserSubs('active'); }
    else { const j = await res.json(); alert('Failed: ' + (j.message || 'Error')); }
}

async function extendSub(id) {
    const months = prompt('Extend by how many months?', '1');
    if (!months || isNaN(months)) return;
    const res = await fetch(`/api/admin/user-subscriptions/${id}/extend`, {
        method: 'POST', headers: hdr(true), body: JSON.stringify({ months: parseInt(months) })
    });
    if (res.ok) { alert('Subscription extended!'); loadUserSubs('active'); }
    else { const j = await res.json(); alert('Failed: ' + (j.message || 'Error')); }
}

// Init
loadPlans();
</script>
@endpush
