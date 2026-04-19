@extends('layouts.app')

@section('title', 'My Profile — AUSHVERA')

@push('styles')
<link rel="stylesheet" href="{{ asset('profile-styles.css') }}">
<style>
    .social-login-separator {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 1.5rem 0 1rem;
        color: #9ca3af;
        font-size: 0.9rem;
    }
    .social-login-separator::before,
    .social-login-separator::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .social-login-separator span {
        padding: 0 10px;
    }
    .social-login-options {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .social-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .social-btn.google-btn {
        background: #ffffff;
        color: #374151;
        border: 1px solid #d1d5db;
    }
    
    .social-btn.google-btn:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
        transform: translateY(-1px);
    }
    
    .social-btn.facebook-btn {
        background: #1877F2;
        color: #ffffff;
        border: 1px solid #1877F2;
    }
    
    .social-btn.facebook-btn:hover {
        background: #166fe5;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(24, 119, 242, 0.4);
    }
</style>
@endpush

@section('content')
@if(session('error'))
<div style="max-width: 800px; margin: 100px auto 0; padding: 15px 20px; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 8px; color: #b91c1c; font-size: 14px; text-align: center;">
    ⚠️ {{ session('error') }}
</div>
@endif
@if(session('success'))
<div style="max-width: 800px; margin: 100px auto 0; padding: 15px 20px; background: #dcfce7; border: 1px solid #86efac; border-radius: 8px; color: #166534; font-size: 14px; text-align: center;">
    ✅ {{ session('success') }}
</div>
@endif
<div class="account-container">
    <!-- Luxury Sidebar -->
    <aside class="account-sidebar">
        <div class="sidebar-header">
            <span class="account-label">ACCOUNT</span>
        </div>
        
        <nav class="sidebar-nav">
            <a href="{{ route('profile') }}" class="nav-item active">
                <span class="nav-indicator"></span>
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/>
                </svg>
                <span class="nav-text">Profile Overview</span>
            </a>
            
            <a href="#" class="nav-item">
                <span class="nav-indicator"></span>
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/>
                </svg>
                <span class="nav-text">My Orders</span>
            </a>
            
            <a href="#" class="nav-item">
                <span class="nav-indicator"></span>
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <span class="nav-text">Addresses</span>
            </a>
            
            <a href="#" class="nav-item">
                <span class="nav-indicator"></span>
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
                <span class="nav-text">Payment Methods</span>
            </a>
            
            <a href="#" class="nav-item">
                <span class="nav-indicator"></span>
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                <span class="nav-text">Wishlist</span>
            </a>
            
            <a href="#" class="nav-item">
                <span class="nav-indicator"></span>
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <span class="nav-text">Security</span>
            </a>

            <a href="/admin" class="nav-item" id="adminPanelLink" style="display: none; border-top: 1px solid rgba(255, 255, 255, 0.1); margin-top: 15px; padding-top: 15px;">
                <span class="nav-indicator"></span>
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="gold" stroke-width="1.5">
                    <path d="M12 2l-10 4v6c0 5 4 10 10 12 6-2 10-7 10-12v-6l-10-4z"/>
                    <path d="M12 6v14"/>
                </svg>
                <span class="nav-text" style="color: gold;">Admin Panel</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <div class="footer-divider"></div>
            <a href="#" class="auth-link" id="authLink" onclick="handleAuthClick(event)">
                <svg class="auth-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                <span id="authText">Login</span>
            </a>
        </div>
    </aside>
    
    <!-- Main Content Preview -->
    <main class="account-main">
        <div class="content-header">
            <h1>Profile Overview</h1>
            <p class="content-subtitle">Manage your personal information and preferences</p>
        </div>
        
        <div class="content-cards">
            <div class="info-card">
                <div class="card-header">
                    <h3>Personal Information</h3>
                    <button class="edit-btn" id="editBtn">Edit</button>
                </div>
                <div class="card-content" id="profileContent">
                    <div class="info-row">
                        <span class="info-label">Full Name</span>
                        <span class="info-value" id="displayName">Guest User</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value" id="displayEmail">Not logged in</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone</span>
                        <span class="info-value" id="displayPhone">Not available</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date of Birth</span>
                        <span class="info-value" id="displayDob">Not provided</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Gender</span>
                        <span class="info-value" id="displayGender">Not provided</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Address</span>
                        <span class="info-value" id="displayAddress">Not provided</span>
                    </div>
                </div>
                <form class="edit-form" id="editForm" style="display: none;">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" id="editName" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="editEmail" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" id="editPhone" required>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" id="editDob">
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select id="editGender">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea id="editAddress" rows="3"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="save-btn">Save Changes</button>
                        <button type="button" class="cancel-btn" id="cancelEdit">Cancel</button>
                    </div>
                </form>
            </div>
            
            {{-- ─── Membership / Subscription Card ─── --}}
            <div class="info-card" id="subscriptionCard">
                <div class="card-header">
                    <h3>Membership & Subscription</h3>
                    <span class="status-badge" id="subBadge" style="display:none;">Active</span>
                </div>
                <div class="card-content" id="subscriptionContent">
                    <p style="color: rgba(255,255,255,0.5); font-size:14px; text-align:center; padding:1rem 0;">
                        Loading subscription info...
                    </p>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Login Modal -->
<div id="loginModal" class="auth-modal">
    <div class="modal-content">
        <span class="close-modal" id="closeLogin">&times;</span>
        <h2>Welcome Back</h2>
        <p class="modal-subtitle">Sign in to your Aushvera account</p>
        <form class="auth-form" id="loginForm">
            <input type="email" id="loginEmail" placeholder="Email Address" required>
            <input type="password" id="loginPassword" placeholder="Password" required>
            <button type="submit" class="auth-btn">Sign In</button>
        </form>
        
        <div class="social-login-separator">
            <span>Or continue with</span>
        </div>
        <div class="social-login-options">
            <a href="{{ route('auth.google') }}" class="social-btn google-btn" style="text-decoration: none;">
                <svg width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Google
            </a>
            <a href="{{ route('auth.facebook') }}" class="social-btn facebook-btn" style="text-decoration: none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Facebook
            </a>
        </div>
        
        <p class="switch-auth" style="margin-top: 0;">Don't have an account? <a href="#" id="showSignup">Sign Up</a></p>
    </div>
</div>

<!-- Signup Modal -->
<div id="signupModal" class="auth-modal">
    <div class="modal-content">
        <span class="close-modal" id="closeSignup">&times;</span>
        <div class="modal-logo">
            <img src="{{ asset('assets/img/logo.png') }}" alt="AUSHVERA Logo" style="width: 60px; height: 60px;">
            <h2 style="margin-top: 1rem;">AUSHVERA</h2>
        </div>
        <p class="modal-subtitle">Create your wellness account</p>
        <form class="auth-form" id="signupForm">
            <input type="text" id="signupFirstName" placeholder="First Name" required>
            <input type="text" id="signupLastName" placeholder="Last Name" required>
            <input type="email" id="signupEmail" placeholder="Email Address" required>
            <input type="tel" id="signupPhone" placeholder="Phone Number" required>
            <input type="password" id="signupPassword" placeholder="Password" required>
            <input type="date" id="signupDob" placeholder="Date of Birth">
            <select id="signupGender">
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" id="signupPincode" placeholder="Pin Code" maxlength="6" required>
            <input type="text" id="signupCity" placeholder="City" readonly>
            <input type="text" id="signupState" placeholder="State" readonly>
            <textarea id="signupAddress" placeholder="Address" rows="2"></textarea>
            <button type="submit" class="auth-btn">Create Account</button>
        </form>

        <div class="social-login-separator">
            <span>Or register with</span>
        </div>
        <div class="social-login-options">
            <a href="{{ route('auth.google') }}" class="social-btn google-btn" style="text-decoration: none;">
                <svg width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Google
            </a>
            <a href="{{ route('auth.facebook') }}" class="social-btn facebook-btn" style="text-decoration: none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Facebook
            </a>
        </div>

        <p class="switch-auth" style="margin-top: 0;">Already have an account? <a href="#" id="showLogin">Sign In</a></p>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('api-config.js') }}"></script>
<script>
    // Handle server-side authenticated user (from OAuth flow)
    @if($user ?? null)
        (function() {
            const serverUser = @json($user ?? []);
            if (serverUser && serverUser.id) {
                localStorage.setItem('auth_token', 'session_auth');
                localStorage.setItem('currentUser', JSON.stringify(serverUser));
                
                // Update display immediately
                const displayName = document.getElementById('displayName');
                const displayEmail = document.getElementById('displayEmail');
                const displayPhone = document.getElementById('displayPhone');
                const displayDob = document.getElementById('displayDob');
                const displayGender = document.getElementById('displayGender');
                const displayAddress = document.getElementById('displayAddress');
                const authText = document.getElementById('authText');
                
                if (displayName) displayName.textContent = serverUser.name || 'User';
                if (displayEmail) displayEmail.textContent = serverUser.email || '';
                if (displayPhone) displayPhone.textContent = serverUser.phone || 'Not provided';
                if (displayDob) displayDob.textContent = serverUser.dob || 'Not provided';
                if (displayGender) displayGender.textContent = serverUser.gender || 'Not provided';
                if (displayAddress) displayAddress.textContent = serverUser.address || 'Not provided';
                if (authText) authText.textContent = 'Logout';
            }
        })();
    @endif
    
    // FORCE CLEAR OLD TOKENS ON PAGE LOAD
    (function() {
        // Clear any old/invalid tokens
        const token = localStorage.getItem('auth_token');
        const user = localStorage.getItem('currentUser');
        
        // If token exists but no user data, clear everything
        if (token && !user) {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('token');
        }
        
        // Update auth text immediately
        const authText = document.getElementById('authText');
        const validToken = localStorage.getItem('auth_token');
        const validUser = localStorage.getItem('currentUser');
        
        if (validToken && validUser) {
            if (authText) authText.textContent = 'Logout';
        } else {
            if (authText) authText.textContent = 'Login';
        }
    })();
    
    // Check if admin and redirect
    (function checkAdminRedirect() {
        const userStr = localStorage.getItem('currentUser');
        if(userStr) {
            try {
                const user = JSON.parse(userStr);
                if (user.role === 'admin' || user.role === 'super_admin') {
                    window.location.href = '/admin';
                }
            } catch(e) {}
        }
    })();
    
    async function handleAuthClick(e) {
        e.preventDefault();
        const token = localStorage.getItem('auth_token');
        
        if (token) {
            try {
                await api.logout();
            } catch (error) {}
            localStorage.removeItem('auth_token');
            localStorage.removeItem('currentUser');
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            alert('Logged out successfully!');
            window.location.reload();
        } else {
            const modal = document.getElementById('loginModal');
            if (modal) modal.classList.add('active');
        }
    }
</script>
<script src="{{ asset('profile-auth.js') }}"></script>
<script>
// ── Subscription System ──────────────────────────────────────────────────────
const SUB_CONTENT = document.getElementById('subscriptionContent');
const SUB_BADGE   = document.getElementById('subBadge');

function getToken() {
    return localStorage.getItem('auth_token');
}

function authHeader() {
    const t = getToken();
    return t ? { 'Authorization': 'Bearer ' + t, 'Content-Type': 'application/json', 'Accept': 'application/json' } : { 'Accept': 'application/json' };
}

// Render "no subscription" state with available plans
function renderPlans(plans) {
    if (!plans || plans.length === 0) {
        SUB_CONTENT.innerHTML = `
            <div class="membership-info">
                <p class="membership-text" style="opacity:0.7;">You don't have an active subscription yet.</p>
                <p style="color:rgba(255,255,255,0.4);font-size:13px;margin-top:8px;">No plans available right now. Check back later!</p>
            </div>`;
        return;
    }
    SUB_CONTENT.innerHTML = `
        <p style="color:rgba(255,255,255,0.5);font-size:13px;margin-bottom:1rem;">Choose a plan to unlock exclusive benefits:</p>
        <div style="display:flex;flex-direction:column;gap:12px;" id="plansList">
            ${plans.map(p => `
                <div style="border:1px solid rgba(184,150,76,0.3);border-radius:10px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;background:rgba(184,150,76,0.05);">
                    <div>
                        <div style="font-weight:600;color:var(--cream, #f7f4ee);font-size:15px;">${p.name}</div>
                        <div style="font-size:12px;color:rgba(255,255,255,0.5);margin-top:3px;">${p.description || ''}</div>
                        <div style="font-size:13px;color:#B8964C;margin-top:4px;">
                            ₹${parseFloat(p.price).toFixed(2)} · ${p.duration_months} month${p.duration_months > 1 ? 's' : ''}
                        </div>
                    </div>
                    <button onclick="subscribeToPlan(${p.id}, '${p.name.replace(/'/g,"\\'")}', ${p.price})"
                        style="background:linear-gradient(135deg,#B8964C,#d4a85a);color:#0b1c2d;border:none;padding:9px 18px;border-radius:7px;font-weight:700;cursor:pointer;font-size:13px;white-space:nowrap;transition:opacity 0.2s;"
                        onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                        Subscribe
                    </button>
                </div>
            `).join('')}
        </div>`;
}

// Render active subscription
function renderActiveSubscription(sub) {
    SUB_BADGE.style.display = '';
    SUB_BADGE.textContent   = '✓ Active';
    SUB_BADGE.style.background = 'rgba(16,185,129,0.15)';
    SUB_BADGE.style.color = '#10b981';
    SUB_BADGE.style.border = '1px solid rgba(16,185,129,0.3)';

    SUB_CONTENT.innerHTML = `
        <div class="membership-info">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:1rem;">
                <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,rgba(184,150,76,0.2),rgba(184,150,76,0.05));display:flex;align-items:center;justify-content:center;font-size:22px;border:1px solid rgba(184,150,76,0.2);">👑</div>
                <div>
                    <div style="font-weight:700;font-size:16px;color:var(--cream,#f7f4ee);">${sub.plan_name}</div>
                    <div style="font-size:12px;color:#B8964C;font-weight:500;">Premium Member</div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:1rem;">
                <div style="background:rgba(255,255,255,0.04);padding:10px;border-radius:8px;border:1px solid rgba(255,255,255,0.06);">
                    <div style="font-size:11px;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.5px;">Started</div>
                    <div style="font-size:13px;color:var(--cream,#f7f4ee);margin-top:3px;font-weight:500;">${sub.starts_at}</div>
                </div>
                <div style="background:rgba(255,255,255,0.04);padding:10px;border-radius:8px;border:1px solid rgba(255,255,255,0.06);">
                    <div style="font-size:11px;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.5px;">Expires</div>
                    <div style="font-size:13px;color:var(--cream,#f7f4ee);margin-top:3px;font-weight:500;">${sub.ends_at}</div>
                </div>
            </div>
            <div style="background:rgba(16,185,129,0.05);border:1px solid rgba(16,185,129,0.15);border-radius:8px;padding:12px;margin-bottom:1rem;">
                <div style="font-size:12px;color:#10b981;font-weight:600;margin-bottom:8px;">🎯 Your Benefits</div>
                <div style="display:flex;flex-direction:column;gap:5px;">
                    <span style="font-size:13px;color:rgba(255,255,255,0.7);">✦ Exclusive access to new launches</span>
                    <span style="font-size:13px;color:rgba(255,255,255,0.7);">✦ Priority customer support</span>
                    <span style="font-size:13px;color:rgba(255,255,255,0.7);">✦ Special Ayurvedic ritual guides</span>
                    <span style="font-size:13px;color:rgba(255,255,255,0.7);">✦ Members-only discounts</span>
                </div>
            </div>
            <button onclick="cancelSubscription()"
                style="border:1px solid rgba(239,68,68,0.4);background:transparent;color:#ef4444;padding:8px 18px;border-radius:7px;cursor:pointer;font-size:13px;transition:all 0.2s;"
                onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='transparent'">
                Cancel Subscription
            </button>
        </div>`;
}

// Render "not logged in" state
function renderLoginRequired() {
    SUB_CONTENT.innerHTML = `
        <div class="membership-info" style="text-align:center;padding:1rem 0;">
            <p class="membership-text" style="opacity:0.7;">Please log in to manage your subscription.</p>
            <button onclick="document.getElementById('loginModal').classList.add('active')"
                style="margin-top:12px;background:linear-gradient(135deg,#B8964C,#d4a85a);color:#0b1c2d;border:none;padding:10px 24px;border-radius:7px;font-weight:700;cursor:pointer;">
                Login to Subscribe
            </button>
        </div>`;
}

// Load subscription data
async function loadSubscriptionData() {
    const token = getToken();

    if (!token || token === 'session_auth') {
        // Try loading plans to show available options
        try {
            const res = await fetch('/api/subscription-plans');
            const json = await res.json();
            if (token === 'session_auth') {
                // Logged in via session, try to get my subscription
                await loadMySubscription();
            } else {
                renderLoginRequired();
            }
        } catch(e) {
            renderLoginRequired();
        }
        return;
    }

    await loadMySubscription();
}

async function loadMySubscription() {
    try {
        const [subRes, planRes] = await Promise.all([
            fetch('/api/my-subscription', { headers: authHeader() }),
            fetch('/api/subscription-plans')
        ]);
        const subJson  = await subRes.json();
        const planJson = await planRes.json();
        const plans    = planJson.data || [];

        if (subRes.ok && subJson.has_subscription) {
            renderActiveSubscription(subJson.subscription);
        } else {
            SUB_BADGE.style.display = 'none';
            renderPlans(plans);
        }
    } catch (e) {
        SUB_CONTENT.innerHTML = '<p style="color:#ef4444;font-size:13px;text-align:center;padding:1rem;">Error loading subscription data</p>';
    }
}

async function subscribeToPlan(planId, planName, price) {
    const token = getToken();
    if (!token || token === 'session_auth') {
        alert('Please log in with your account credentials to subscribe to a plan.');
        document.getElementById('loginModal')?.classList.add('active');
        return;
    }
    if (!confirm(`Subscribe to "${planName}" for ₹${parseFloat(price).toFixed(2)}?`)) return;

    try {
        const res = await fetch('/api/subscribe', {
            method: 'POST',
            headers: authHeader(),
            body: JSON.stringify({ subscription_id: planId })
        });
        const json = await res.json();
        if (res.ok && json.success) {
            alert('🎉 ' + json.message);
            await loadMySubscription();
        } else {
            alert('Failed: ' + (json.message || 'Unknown error'));
        }
    } catch(e) {
        alert('Network error. Please try again.');
    }
}

async function cancelSubscription() {
    if (!confirm('Are you sure you want to cancel your subscription? You will lose access to premium benefits.')) return;

    try {
        const res = await fetch('/api/subscription/cancel', {
            method: 'POST',
            headers: authHeader()
        });
        const json = await res.json();
        if (res.ok && json.success) {
            alert(json.message);
            SUB_BADGE.style.display = 'none';
            await loadMySubscription();
        } else {
            alert('Failed: ' + (json.message || 'Error'));
        }
    } catch(e) {
        alert('Network error.');
    }
}

// Auto-load when page is ready
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(loadSubscriptionData, 300); // slight delay so auth token is set
});
</script>
@endpush
