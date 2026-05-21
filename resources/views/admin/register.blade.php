@extends('layouts.admin')
@section('title', 'Create Admin')

@push('styles')
<style>
.reg-wrap { padding: 24px 28px; font-family: 'Inter', sans-serif; max-width: 600px; }

.reg-card {
  background: #fff; border: 1px solid #e9ecef; border-radius: 14px; padding: 28px;
}
.reg-card h2 { font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px; }
.reg-card .subtitle { font-size: 13px; color: #9ca3af; margin-bottom: 24px; }

.reg-group { margin-bottom: 18px; }
.reg-group label { display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .5px; }
.reg-group label span { color: #ef4444; }
.reg-input {
  width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px;
  font-size: 14px; font-family: 'Inter', sans-serif; transition: border-color .2s;
  background: #fafafa;
}
.reg-input:focus { outline: none; border-color: #c9a96e; box-shadow: 0 0 0 3px rgba(201,169,110,.1); background: #fff; }

.reg-select {
  width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px;
  font-size: 14px; font-family: 'Inter', sans-serif; background: #fafafa;
  cursor: pointer;
}
.reg-select:focus { outline: none; border-color: #c9a96e; }

.reg-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

.reg-btn {
  width: 100%; padding: 12px; border: none; border-radius: 8px;
  background: linear-gradient(135deg, #c9a96e, #b8964c); color: #fff;
  font-size: 14px; font-weight: 600; cursor: pointer; transition: opacity .2s;
  font-family: 'Inter', sans-serif; margin-top: 8px;
}
.reg-btn:hover { opacity: .9; }
.reg-btn:disabled { opacity: .5; cursor: not-allowed; }

.reg-alert {
  padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; display: none;
}
.reg-alert.success { background: #d1fae5; color: #065f46; border: 1px solid #86efac; display: block; }
.reg-alert.error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; display: block; }
</style>
@endpush

@section('content')
<div class="reg-wrap">
  <div class="reg-card">
    <h2>👑 Create Admin Account</h2>
    <p class="subtitle">Add a new admin, manager, or staff member</p>

    <div id="regAlert" class="reg-alert"></div>

    <form id="adminRegisterForm">
      <div class="reg-group">
        <label>Full Name <span>*</span></label>
        <input type="text" id="regName" class="reg-input" required placeholder="e.g. John Doe">
      </div>

      <div class="reg-row">
        <div class="reg-group">
          <label>Email <span>*</span></label>
          <input type="email" id="regEmail" class="reg-input" required placeholder="admin@example.com">
        </div>
        <div class="reg-group">
          <label>Password <span>*</span></label>
          <input type="password" id="regPassword" class="reg-input" required minlength="6" placeholder="Min 6 characters">
        </div>
      </div>

      <div class="reg-group">
        <label>Admin Level <span>*</span></label>
        <select id="regLevel" class="reg-select">
          <option value="staff">👤 Staff — View orders & products (read-only)</option>
          <option value="manager">🏢 Manager — Full product, order & user management</option>
          <option value="super">👑 Super Admin — Full access including admin management</option>
        </select>
      </div>

      <button type="submit" class="reg-btn" id="regBtn">Create Admin Account</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
const token = (localStorage.getItem('admin_token') || localStorage.getItem('auth_token'));
const form = document.getElementById('adminRegisterForm');
const alertBox = document.getElementById('regAlert');

function showAlert(msg, type) {
    alertBox.textContent = msg;
    alertBox.className = 'reg-alert ' + type;
    setTimeout(() => { if (type === 'success') alertBox.style.display = 'none'; }, 5000);
}

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('regBtn');
    btn.disabled = true;
    btn.textContent = 'Creating...';
    alertBox.style.display = 'none';

    const levelMap = { staff: 'admin', manager: 'admin', super: 'super_admin' };
    const level = document.getElementById('regLevel').value;

    const data = {
        name: document.getElementById('regName').value.trim(),
        email: document.getElementById('regEmail').value.trim(),
        password: document.getElementById('regPassword').value,
        admin_role: level === 'super' ? 'super_admin' : 'manager',
        role: levelMap[level] || 'admin'
    };

    try {
        const res = await fetch('{{ url("/api/admin/register") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...(token ? { 'Authorization': 'Bearer ' + token } : {})
            },
            body: JSON.stringify(data)
        });

        const result = await res.json();

        if (res.ok && result.success) {
            showAlert('✅ Admin account created successfully! (' + data.email + ')', 'success');
            form.reset();
        } else {
            const errMsg = result.errors
                ? Object.values(result.errors).flat().join(', ')
                : (result.message || 'Failed to create admin');
            showAlert('❌ ' + errMsg, 'error');
        }
    } catch (error) {
        showAlert('❌ Network error. Please try again.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Create Admin Account';
    }
});
</script>
@endpush
