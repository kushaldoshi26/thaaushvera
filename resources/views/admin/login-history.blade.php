@extends('layouts.admin')
@section('title', 'Login History')

@push('styles')
<style>
.lh-wrap { padding: 24px 28px; font-family: 'Inter', sans-serif; }

/* Stats Cards */
.lh-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
.lh-stat {
  background: #fff; border: 1px solid #e9ecef; border-radius: 14px;
  padding: 20px; display: flex; align-items: center; gap: 14px;
  transition: transform .2s, box-shadow .2s;
}
.lh-stat:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.06); }
.lh-stat-icon {
  width: 46px; height: 46px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;
}
.lh-stat-icon.total   { background: #eff6ff; }
.lh-stat-icon.google  { background: #fef3c7; }
.lh-stat-icon.email   { background: #f0fdf4; }
.lh-stat-icon.recent  { background: #f5f3ff; }
.lh-stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .7px; color: #6b7280; margin-bottom: 2px; }
.lh-stat-value { font-size: 24px; font-weight: 700; color: #111827; line-height: 1; }

/* Filters */
.lh-filters {
  display: flex; align-items: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;
  background: #fff; border: 1px solid #e9ecef; border-radius: 14px; padding: 16px 20px;
}
.lh-filters label { font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; }
.lh-filter-btn {
  padding: 7px 16px; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff;
  font-size: 13px; font-weight: 500; cursor: pointer; transition: all .2s; color: #374151;
  display: flex; align-items: center; gap: 6px; font-family: 'Inter', sans-serif;
}
.lh-filter-btn:hover { border-color: #c9a96e; color: #c9a96e; }
.lh-filter-btn.active { background: #c9a96e; color: #fff; border-color: #c9a96e; }
.lh-search {
  margin-left: auto; padding: 8px 14px; border-radius: 8px; border: 1px solid #e5e7eb;
  font-size: 13px; width: 240px; font-family: 'Inter', sans-serif;
  transition: border-color .2s;
}
.lh-search:focus { outline: none; border-color: #c9a96e; box-shadow: 0 0 0 3px rgba(201,169,110,.1); }

/* Table */
.lh-table-card {
  background: #fff; border: 1px solid #e9ecef; border-radius: 14px;
  overflow: hidden;
}
.lh-table-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 18px 22px; border-bottom: 1px solid #f3f4f6;
}
.lh-table-header h3 { font-size: 15px; font-weight: 600; color: #374151; margin: 0; }
.lh-table-count { font-size: 12px; color: #9ca3af; }

table.lh-table { width: 100%; border-collapse: collapse; }
table.lh-table thead th {
  font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .6px;
  color: #9ca3af; padding: 12px 16px; text-align: left; border-bottom: 1px solid #f3f4f6;
  background: #fafafa;
}
table.lh-table tbody td {
  font-size: 13px; color: #374151; padding: 14px 16px; border-bottom: 1px solid #f9fafb;
  vertical-align: middle;
}
table.lh-table tbody tr:last-child td { border-bottom: none; }
table.lh-table tbody tr:hover { background: #fafbfc; }

/* Badges */
.lh-badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
}
.lh-badge.google   { background: #fef3c7; color: #92400e; }
.lh-badge.facebook { background: #dbeafe; color: #1e40af; }
.lh-badge.email    { background: #d1fae5; color: #065f46; }
.lh-badge.api      { background: #f3e8ff; color: #6b21a8; }
.lh-badge.otp      { background: #fce7f3; color: #9d174d; }
.lh-badge.unknown  { background: #f3f4f6; color: #6b7280; }

.lh-role {
  display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
}
.lh-role.super_admin { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
.lh-role.admin { background: #fee2e2; color: #991b1b; }
.lh-role.user  { background: #dbeafe; color: #1e40af; }

/* User info cell */
.lh-user-cell { display: flex; align-items: center; gap: 10px; }
.lh-user-avatar {
  width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #c9a96e, #deb887);
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 700; color: #fff; flex-shrink: 0;
}
.lh-user-name { font-weight: 500; color: #111827; }
.lh-user-email { font-size: 11px; color: #9ca3af; }

/* Loading & empty */
.lh-loading { text-align: center; padding: 40px; color: #9ca3af; }
.spin { display: inline-block; width: 20px; height: 20px; border: 2px solid #e5e7eb; border-top-color: #c9a96e; border-radius: 50%; animation: sp .7s linear infinite; }
@keyframes sp { to { transform: rotate(360deg); } }

.lh-empty { text-align: center; padding: 60px 20px; color: #9ca3af; }
.lh-empty-icon { font-size: 48px; margin-bottom: 12px; }
.lh-empty-text { font-size: 15px; font-weight: 500; margin-bottom: 4px; }
.lh-empty-sub { font-size: 13px; }

/* IP & agent tooltip */
.lh-ip { font-family: 'Courier New', monospace; font-size: 12px; color: #6b7280; }
.lh-time { font-size: 13px; color: #374151; }
.lh-time-ago { font-size: 11px; color: #9ca3af; display: block; }

/* Pagination */
.lh-pagination { display: flex; justify-content: center; gap: 6px; padding: 16px; }
.lh-page-btn {
  padding: 6px 12px; border-radius: 6px; border: 1px solid #e5e7eb;
  background: #fff; font-size: 12px; cursor: pointer; transition: all .2s;
  font-family: 'Inter', sans-serif;
}
.lh-page-btn:hover { border-color: #c9a96e; color: #c9a96e; }
.lh-page-btn.active { background: #c9a96e; color: #fff; border-color: #c9a96e; }
.lh-page-btn:disabled { opacity: .4; cursor: not-allowed; }

/* Responsive */
@media (max-width: 768px) {
  .lh-stats { grid-template-columns: repeat(2, 1fr); }
  .lh-filters { flex-direction: column; align-items: stretch; }
  .lh-search { margin-left: 0; width: 100%; }
  table.lh-table { font-size: 12px; }
  table.lh-table thead th,
  table.lh-table tbody td { padding: 10px 12px; }
}
</style>
@endpush

@section('content')
<div class="lh-wrap">

  {{-- Stats Cards --}}
  <div class="lh-stats">
    <div class="lh-stat">
      <div class="lh-stat-icon total">📊</div>
      <div>
        <div class="lh-stat-label">Total Logins</div>
        <div class="lh-stat-value" id="statTotal"><span class="spin"></span></div>
      </div>
    </div>
    <div class="lh-stat">
      <div class="lh-stat-icon google">🔑</div>
      <div>
        <div class="lh-stat-label">Google Logins</div>
        <div class="lh-stat-value" id="statGoogle"><span class="spin"></span></div>
      </div>
    </div>
    <div class="lh-stat">
      <div class="lh-stat-icon email">📧</div>
      <div>
        <div class="lh-stat-label">Email Logins</div>
        <div class="lh-stat-value" id="statEmail"><span class="spin"></span></div>
      </div>
    </div>
    <div class="lh-stat">
      <div class="lh-stat-icon recent">🕐</div>
      <div>
        <div class="lh-stat-label">Today</div>
        <div class="lh-stat-value" id="statToday"><span class="spin"></span></div>
      </div>
    </div>
  </div>

  {{-- Filters --}}
  <div class="lh-filters">
    <label>Filter:</label>
    <button class="lh-filter-btn active" data-filter="all">All</button>
    <button class="lh-filter-btn" data-filter="google">🔑 Google</button>
    <button class="lh-filter-btn" data-filter="email">📧 Email</button>
    <button class="lh-filter-btn" data-filter="facebook">📘 Facebook</button>
    <button class="lh-filter-btn" data-filter="api">⚡ API</button>
    <input type="text" class="lh-search" id="lhSearch" placeholder="Search by name or email...">
  </div>

  {{-- Table --}}
  <div class="lh-table-card">
    <div class="lh-table-header">
      <h3>Login Activity Log</h3>
      <span class="lh-table-count" id="lhCount"></span>
    </div>
    <table class="lh-table">
      <thead>
        <tr>
          <th>User</th>
          <th>Role</th>
          <th>Method</th>
          <th>Login Time</th>
          <th>IP Address</th>
        </tr>
      </thead>
      <tbody id="lhBody">
        <tr><td colspan="5" class="lh-loading"><span class="spin"></span> Loading login history...</td></tr>
      </tbody>
    </table>
    <div class="lh-pagination" id="lhPagination"></div>
  </div>

</div>
@endsection

@push('scripts')
<script>
const token = (localStorage.getItem('admin_token') || localStorage.getItem('auth_token'));
const authHeaders = token ? { 'Authorization': 'Bearer ' + token } : {};

let allLogs = [];
let filteredLogs = [];
let currentFilter = 'all';
let currentPage = 1;
const perPage = 25;

async function loadLoginHistory() {
    try {
        const response = await fetch('{{ url("/api/admin/login-history") }}', {
            headers: authHeaders
        });

        if (!response.ok) {
            throw new Error('Failed to fetch login history (HTTP ' + response.status + ')');
        }

        allLogs = await response.json();
        applyFilters();
        updateStats();
    } catch (error) {
        console.error('Error loading login history:', error);
        document.getElementById('lhBody').innerHTML =
            '<tr><td colspan="5" class="lh-empty">' +
            '<div class="lh-empty-icon">⚠️</div>' +
            '<div class="lh-empty-text">Error loading login history</div>' +
            '<div class="lh-empty-sub">' + error.message + '</div>' +
            '</td></tr>';
    }
}

function updateStats() {
    const total = allLogs.length;
    const google = allLogs.filter(l => l.login_method === 'google').length;
    const email = allLogs.filter(l => l.login_method === 'email' || l.login_method === 'api').length;

    const today = new Date().toDateString();
    const todayCount = allLogs.filter(l => new Date(l.login_time).toDateString() === today).length;

    document.getElementById('statTotal').textContent = total;
    document.getElementById('statGoogle').textContent = google;
    document.getElementById('statEmail').textContent = email;
    document.getElementById('statToday').textContent = todayCount;
}

function applyFilters() {
    const search = document.getElementById('lhSearch').value.toLowerCase().trim();

    filteredLogs = allLogs.filter(log => {
        // Method filter
        if (currentFilter !== 'all') {
            if (currentFilter === 'email') {
                if (log.login_method !== 'email' && log.login_method !== 'api') return false;
            } else {
                if (log.login_method !== currentFilter) return false;
            }
        }
        // Search filter
        if (search) {
            const name = (log.name || '').toLowerCase();
            const email = (log.email || '').toLowerCase();
            if (!name.includes(search) && !email.includes(search)) return false;
        }
        return true;
    });

    currentPage = 1;
    renderTable();
    renderPagination();
}

function renderTable() {
    const tbody = document.getElementById('lhBody');
    const start = (currentPage - 1) * perPage;
    const pageData = filteredLogs.slice(start, start + perPage);

    document.getElementById('lhCount').textContent =
        `Showing ${Math.min(start + 1, filteredLogs.length)}–${Math.min(start + perPage, filteredLogs.length)} of ${filteredLogs.length}`;

    if (pageData.length === 0) {
        tbody.innerHTML =
            '<tr><td colspan="5" class="lh-empty">' +
            '<div class="lh-empty-icon">📋</div>' +
            '<div class="lh-empty-text">No login records found</div>' +
            '<div class="lh-empty-sub">Login events will appear here as users sign in</div>' +
            '</td></tr>';
        return;
    }

    tbody.innerHTML = pageData.map(log => {
        const initials = (log.name || '?').split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
        const method = log.login_method || 'unknown';
        const methodIcon = getMethodIcon(method);
        const methodLabel = getMethodLabel(method);
        const loginTime = new Date(log.login_time);
        const timeAgo = getTimeAgo(loginTime);
        const roleClass = log.role || 'user';

        return `
            <tr>
                <td>
                    <div class="lh-user-cell">
                        <div class="lh-user-avatar">${initials}</div>
                        <div>
                            <div class="lh-user-name">${escapeHtml(log.name || 'Unknown')}</div>
                            <div class="lh-user-email">${escapeHtml(log.email || '')}</div>
                        </div>
                    </div>
                </td>
                <td><span class="lh-role ${roleClass}">${getRoleLabel(log.role)}</span></td>
                <td><span class="lh-badge ${method}">${methodIcon} ${methodLabel}</span></td>
                <td>
                    <span class="lh-time">${loginTime.toLocaleString('en-IN', { dateStyle: 'medium', timeStyle: 'short' })}</span>
                    <span class="lh-time-ago">${timeAgo}</span>
                </td>
                <td><span class="lh-ip">${log.ip_address || 'N/A'}</span></td>
            </tr>
        `;
    }).join('');
}

function renderPagination() {
    const totalPages = Math.ceil(filteredLogs.length / perPage);
    const container = document.getElementById('lhPagination');

    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '';
    html += `<button class="lh-page-btn" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>← Prev</button>`;

    for (let i = 1; i <= totalPages; i++) {
        if (totalPages > 7 && (i > 3 && i < totalPages - 2) && (i < currentPage - 1 || i > currentPage + 1)) {
            if (i === 4) html += `<span style="padding:0 8px;color:#9ca3af">…</span>`;
            continue;
        }
        html += `<button class="lh-page-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
    }

    html += `<button class="lh-page-btn" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>Next →</button>`;
    container.innerHTML = html;
}

function goToPage(page) {
    const totalPages = Math.ceil(filteredLogs.length / perPage);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderTable();
    renderPagination();
    // Scroll to top of table
    document.querySelector('.lh-table-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function getMethodIcon(method) {
    const icons = { google: '🔑', facebook: '📘', email: '📧', api: '⚡', otp: '📱' };
    return icons[method] || '❓';
}

function getMethodLabel(method) {
    const labels = { google: 'Google', facebook: 'Facebook', email: 'Email', api: 'API', otp: 'OTP' };
    return labels[method] || method;
}

function getRoleLabel(role) {
    const labels = { super_admin: '👑 Super Admin', admin: '🛡️ Admin', user: '👤 User' };
    return labels[role] || role || 'User';
}

function getTimeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    if (seconds < 60) return 'Just now';
    if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
    if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';
    if (seconds < 604800) return Math.floor(seconds / 86400) + 'd ago';
    return Math.floor(seconds / 604800) + 'w ago';
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Filter button handlers
document.querySelectorAll('.lh-filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lh-filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFilter = btn.dataset.filter;
        applyFilters();
    });
});

// Search handler
let searchTimeout;
document.getElementById('lhSearch').addEventListener('input', () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

// Load on page ready
loadLoginHistory();
</script>
@endpush
