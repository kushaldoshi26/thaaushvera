@extends('layouts.admin')
@section('title', 'Users')

@section('content')
<div class="page-bar">
    <div class="page-bar-title">Users</div>
    <div class="search-bar">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search by name or email..." oninput="filterUsers(this.value)">
    </div>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(3,1fr); margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon purple">👥</div>
        <div><div class="stat-label">Total Users</div><div class="stat-value">{{ $users->count() }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div><div class="stat-label">Verified</div><div class="stat-value">{{ $users->whereNotNull('email_verified_at')->count() }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">📅</div>
        <div><div class="stat-label">This Month</div><div class="stat-value">{{ $users->where('created_at', '>=', now()->startOfMonth())->count() }}</div></div>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Verified</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="usersBody">
                @forelse($users as $user)
                <tr class="user-row" data-search="{{ strtolower($user->name . ' ' . $user->email) }}">
                    <td class="text-muted text-sm">{{ $user->id }}</td>
                    <td>
                        <div class="flex items-center gap-1">
                            <div style="width:36px; height:36px; border-radius:50%; background:rgba(201,169,110,0.15); display:flex; align-items:center; justify-content:center; font-weight:700; color:var(--admin-gold); font-size:0.875rem; flex-shrink:0;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <strong>{{ $user->name }}</strong>
                        </div>
                    </td>
                    <td class="text-muted">{{ $user->email }}</td>
                    <td>
                        <span class="badge {{ $user->role === 'admin' ? 'badge-processing' : 'badge-active' }}">
                            {{ ucfirst($user->role ?? 'user') }}
                        </span>
                    </td>
                    <td>
                        @if($user->email_verified_at)
                            <span class="badge badge-paid">Verified</span>
                        @else
                            <span class="badge badge-pending">Unverified</span>
                        @endif
                    </td>
                    <td class="text-muted text-sm">{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')">Remove</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center; padding:2.5rem; color:var(--admin-muted);">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function filterUsers(q) {
    document.querySelectorAll('.user-row').forEach(r => {
        r.style.display = r.dataset.search.includes(q.toLowerCase()) ? '' : 'none';
    });
}

function deleteUser(id, name) {
    if (!confirm(`Remove user "${name}"? This cannot be undone.`)) return;
    const token = localStorage.getItem('auth_token');
    fetch(`/api/admin/users/${id}`, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, ...(token ? { Authorization: 'Bearer ' + token } : {}) }
    }).then(res => res.ok ? window.location.reload() : res.json().then(e => alert(e.message || 'Failed')))
      .catch(() => alert('Network error'));
}
</script>
@endpush
