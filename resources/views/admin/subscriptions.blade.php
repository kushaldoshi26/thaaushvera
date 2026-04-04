@extends('layouts.admin')
@section('title', 'Subscriptions')
@section('page-title', 'Subscription Offers')

@section('header-actions')
<button onclick="openAddSubscriptionModal()" style="background:#059669;color:white;padding:8px 16px;border:none;border-radius:6px;cursor:pointer;font-weight:500;">+ Add Subscription Offer</button>
@endsection

@section('content')
<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Duration (months)</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="subscriptionsTable">
            <tr><td colspan="6" class="text-center">Loading...</td></tr>
        </tbody>
    </table>
</div>

<!-- Add/Edit Subscription Modal -->
<div id="subscriptionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="bg-gray-900 text-white px-6 py-4 rounded-t-lg flex justify-between items-center">
            <h2 class="text-xl font-bold" id="subModalTitle">Add Subscription Offer</h2>
            <button onclick="closeSubscriptionModal()" class="text-2xl hover:text-gray-300">&times;</button>
        </div>
        <form id="subscriptionForm" class="p-6">
            <input type="hidden" id="subscriptionId">

            <div class="form-group" style="margin-bottom:12px;">
                <label style="display:block;font-weight:500;margin-bottom:6px;">Offer Name</label>
                <input type="text" id="subscriptionName" required style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
            </div>

            <div class="form-group" style="margin-bottom:12px;display:flex;gap:8px;">
                <div style="flex:1;">
                    <label style="display:block;font-weight:500;margin-bottom:6px;">Price</label>
                    <input type="number" id="subscriptionPrice" step="0.01" required style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                </div>
                <div style="width:140px;">
                    <label style="display:block;font-weight:500;margin-bottom:6px;">Duration (months)</label>
                    <input type="number" id="subscriptionDuration" min="1" value="1" required style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom:12px;">
                <label style="display:block;font-weight:500;margin-bottom:6px;">Description</label>
                <textarea id="subscriptionDescription" rows="3" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;resize:vertical;"></textarea>
            </div>

            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                <input type="checkbox" id="subscriptionActive" checked>
                <label for="subscriptionActive">Active</label>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:8px;">
                <button type="button" onclick="closeSubscriptionModal()" style="padding:8px 16px;background:#6b7280;color:white;border:none;border-radius:6px;cursor:pointer;">Cancel</button>
                <button type="submit" style="padding:8px 16px;background:#2563eb;color:white;border:none;border-radius:6px;cursor:pointer;">Save Offer</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const subToken = api.getToken();
const subHeaders = { 'Authorization': `Bearer ${subToken}`, 'Accept': 'application/json' };

async function loadSubscriptions() {
    try {
        const res = await fetch('{{ url("/api/admin/subscriptions") }}', { headers: subHeaders });
        const data = await res.json();
        const subs = data.data || data || [];
        const tbody = document.getElementById('subscriptionsTable');
        if (subs.length > 0) {
            tbody.innerHTML = subs.map(s => `
                <tr>
                    <td>${s.id}</td>
                    <td>${s.name}</td>
                    <td>${s.price}</td>
                    <td>${s.duration_months || ''}</td>
                    <td>${s.active ? 'Yes' : 'No'}</td>
                    <td>
                        <button class="btn btn-primary" onclick="editSubscription(${s.id}, '${(s.name||'').replace(/'/g,"\\'")}', ${s.price}, ${s.duration_months||1}, '${(s.description||'').replace(/'/g,"\\'")}', ${s.active ? 1 : 0})">Edit</button>
                        <button class="btn btn-danger" onclick="deleteSubscription(${s.id})">Delete</button>
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">No subscription offers yet.</td></tr>';
        }
    } catch (e) {
        document.getElementById('subscriptionsTable').innerHTML = '<tr><td colspan="6" class="text-center">Error loading offers</td></tr>';
    }
}

function openAddSubscriptionModal() {
    document.getElementById('subModalTitle').textContent = 'Add Subscription Offer';
    document.getElementById('subscriptionForm').reset();
    document.getElementById('subscriptionId').value = '';
    document.getElementById('subscriptionActive').checked = true;
    showSubscriptionModal();
}

function editSubscription(id, name, price, duration, description, active) {
    document.getElementById('subModalTitle').textContent = 'Edit Subscription Offer';
    document.getElementById('subscriptionId').value = id;
    document.getElementById('subscriptionName').value = name;
    document.getElementById('subscriptionPrice').value = price;
    document.getElementById('subscriptionDuration').value = duration;
    document.getElementById('subscriptionDescription').value = description;
    document.getElementById('subscriptionActive').checked = !!active;
    showSubscriptionModal();
}

async function deleteSubscription(id) {
    if (!confirm('Delete this subscription offer?')) return;
    try {
        const res = await fetch(`{{ url('/api/admin/subscriptions') }}/${id}`, { method: 'DELETE', headers: subHeaders });
        if (res.ok) {
            alert('Deleted');
            loadSubscriptions();
        } else {
            const err = await res.json();
            alert('Failed: ' + (err.message || 'Unknown'));
        }
    } catch (e) {
        alert('Error deleting');
    }
}

function showSubscriptionModal() {
    const el = document.getElementById('subscriptionModal');
    el.classList.remove('hidden');
    el.classList.add('flex');
}
function closeSubscriptionModal() {
    const el = document.getElementById('subscriptionModal');
    el.classList.add('hidden');
    el.classList.remove('flex');
}

document.getElementById('subscriptionForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('subscriptionId').value;
    const payload = {
        name: document.getElementById('subscriptionName').value,
        price: parseFloat(document.getElementById('subscriptionPrice').value) || 0,
        duration_months: parseInt(document.getElementById('subscriptionDuration').value) || 1,
        description: document.getElementById('subscriptionDescription').value || null,
        active: document.getElementById('subscriptionActive').checked ? 1 : 0,
    };
    const url = id ? `{{ url('/api/admin/subscriptions') }}/${id}` : '{{ url('/api/admin/subscriptions') }}';
    try {
        const res = await fetch(url, {
            method: id ? 'PUT' : 'POST',
            headers: { ...subHeaders, 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        if (res.ok) {
            alert(id ? 'Updated' : 'Created');
            closeSubscriptionModal();
            loadSubscriptions();
        } else {
            const err = await res.json();
            alert('Failed: ' + (err.message || JSON.stringify(err.errors || {})));
        }
    } catch (e) {
        alert('Error saving subscription');
    }
});

loadSubscriptions();
</script>
@endpush