@extends('layouts.admin')
@section('title', 'Credentials Generator')
@section('page-title', 'Admin Credentials Generator')

@section('content')
<style>
    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: #1f2937;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-container {
        max-width: 600px;
    }

    .btn {
        padding: 10px 16px;
        border-radius: 6px;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
        width: 100%;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .credentials-display {
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 20px;
        margin-top: 20px;
        display: none;
    }

    .credentials-display.active {
        display: block;
    }

    .credential-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        background: white;
        border-radius: 4px;
        margin-bottom: 8px;
        border: 1px solid #e5e7eb;
    }

    .credential-label {
        font-weight: 500;
        color: #6b7280;
        width: 120px;
    }

    .credential-value {
        flex: 1;
        font-family: 'Courier New', monospace;
        background: #f3f4f6;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 13px;
        word-break: break-all;
    }

    .copy-btn {
        padding: 6px 10px;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        margin-left: 8px;
    }

    .copy-btn:hover {
        background: #2563eb;
    }

    .qr-code-container {
        text-align: center;
        padding: 20px;
        background: white;
        border-radius: 6px;
        margin-top: 16px;
    }

    .qr-code-container img {
        max-width: 200px;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 16px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }

    .alert-error {
        background: #fee2e2;
        color: #7f1d1d;
        border: 1px solid #fca5a5;
    }

    .two-factor-options {
        background: #f9fafb;
        padding: 16px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        margin-bottom: 16px;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .checkbox-label input[type="checkbox"] {
        width: auto;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 16px;
        flex-wrap: wrap;
    }

    .action-buttons .btn {
        flex: 1;
        min-width: 120px;
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background: #4b5563;
    }

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-success:hover {
        background: #059669;
    }
</style>

<div class="form-container">
    <p style="color: #6b7280; margin-bottom: 20px;">Create secure admin accounts with automatic password generation and 2FA setup</p>

    <form id="credentialsForm" onsubmit="generateCredentials(event)">
        <div class="form-group">
            <label>Admin Name *</label>
            <input type="text" id="adminName" required placeholder="e.g., John Manager">
        </div>

        <div class="form-group">
            <label>Email Address *</label>
            <input type="email" id="adminEmail" required placeholder="e.g., john@aushvera.com">
        </div>

        <div class="form-group">
            <label>Admin Role *</label>
            <select id="adminRole" required>
                <option value="">Select a role</option>
                <option value="manager">Manager</option>
                <option value="support">Support Staff</option>
                <option value="super_admin">Super Admin</option>
            </select>
        </div>

        <div class="two-factor-options">
            <h3 style="margin: 0 0 10px 0; font-weight: bold;">Security Options</h3>
            <label class="checkbox-label">
                <input type="checkbox" id="enable2FA" checked>
                <span>Enable Two-Factor Authentication (2FA)</span>
            </label>
            <p style="color: #6b7280; font-size: 13px; margin-top: 8px;">
                ℹ️ When enabled, admin will scan QR code with Google Authenticator or similar app
            </p>
        </div>

        <div id="alertContainer"></div>

        <button type="submit" class="btn btn-primary">Generate Credentials</button>
    </form>

    <div id="credentialsDisplay" class="credentials-display">
        <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 16px;">✓ Credentials Generated Successfully</h2>

        <div class="credential-row">
            <span class="credential-label">Admin ID:</span>
            <span class="credential-value" id="displayAdminId">-</span>
            <button class="copy-btn" type="button" onclick="copyToClipboard('displayAdminId')">Copy</button>
        </div>

        <div class="credential-row">
            <span class="credential-label">Email:</span>
            <span class="credential-value" id="displayEmail">-</span>
            <button class="copy-btn" type="button" onclick="copyToClipboard('displayEmail')">Copy</button>
        </div>

        <div class="credential-row">
            <span class="credential-label">Password:</span>
            <span class="credential-value" id="displayPassword">-</span>
            <button class="copy-btn" type="button" onclick="copyToClipboard('displayPassword')">Copy</button>
        </div>

        <div class="credential-row">
            <span class="credential-label">Role:</span>
            <span class="credential-value" id="displayRole">-</span>
        </div>

        <div id="twoFactorSection" style="display: none; margin-top: 20px;">
            <h3 style="font-weight: bold; margin-bottom: 12px;">Two-Factor Authentication Setup</h3>
            
            <div class="qr-code-container">
                <p style="color: #6b7280; margin-bottom: 12px;">Scan this QR code with Google Authenticator:</p>
                <img id="qrCodeImg" alt="QR Code" style="border: 1px solid #e5e7eb; padding: 10px; border-radius: 4px;">
            </div>

            <div class="credential-row">
                <span class="credential-label">Secret Key:</span>
                <span class="credential-value" id="displaySecret">-</span>
                <button class="copy-btn" type="button" onclick="copyToClipboard('displaySecret')">Copy</button>
            </div>

            <div style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 4px; padding: 12px; margin-top: 12px; font-size: 13px;">
                <strong>⚠️ Important:</strong> Provide both the QR code and secret key to the admin. They should scan the QR code OR enter the secret key manually in their authenticator app.
            </div>
        </div>

        <div class="action-buttons">
            <button type="button" class="btn btn-success" onclick="closeCredentialsDisplay()">✓ New Admin</button>
        </div>
    </div>
</div>

<script>
    const API_URL = 'http://127.0.0.1:8000/api';
    const token = localStorage.getItem('auth_token') || localStorage.getItem('token');
    
    // Redirect to login if no token
    if (!token) {
        window.location.href = '/admin';
    }

    function showAlert(message, type = 'success') {
        const container = document.getElementById('alertContainer');
        container.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => container.innerHTML = '', 5000);
    }

    async function generateCredentials(event) {
        event.preventDefault();

        const formData = {
            name: document.getElementById('adminName').value,
            email: document.getElementById('adminEmail').value,
            admin_role: document.getElementById('adminRole').value,
            is_2fa_enabled: document.getElementById('enable2FA').checked
        };

        try {
            const response = await fetch(`${API_URL}/admin/generator/credentials`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) {
                const error = await response.json();
                showAlert(error.message || 'Failed to generate credentials', 'error');
                return;
            }

            const data = await response.json();
            const creds = data.credentials;

            // Display credentials
            document.getElementById('displayAdminId').textContent = creds.admin_id;
            document.getElementById('displayEmail').textContent = creds.email;
            document.getElementById('displayPassword').textContent = creds.temporary_password;
            document.getElementById('displayRole').textContent = formData.admin_role;

            // Show 2FA section if enabled
            if (creds.two_factor_secret) {
                document.getElementById('twoFactorSection').style.display = 'block';
                document.getElementById('displaySecret').textContent = creds.two_factor_secret.secret;
                document.getElementById('display2FACode').textContent = creds.admin_id; // Use admin ID as setup code
                document.getElementById('qrCodeImg').src = creds.two_factor_secret.qr_code_url;
            } else {
                document.getElementById('twoFactorSection').style.display = 'none';
            }

            document.getElementById('credentialsDisplay').classList.add('active');
            showAlert('Credentials generated successfully! Admin must change password on first login.');
        } catch (error) {
            console.error('Error:', error);
            showAlert('An error occurred while generating credentials', 'error');
        }
    }

    function copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        const text = element.textContent;
        navigator.clipboard.writeText(text).then(() => {
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = '✓ Copied';
            setTimeout(() => btn.textContent = originalText, 2000);
        });
    }

    function closeCredentialsDisplay() {
        document.getElementById('credentialsDisplay').classList.remove('active');
        document.getElementById('credentialsForm').reset();
    }
</script>
@endsection
