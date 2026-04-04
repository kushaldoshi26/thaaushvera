<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login — AUSHVERA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0B1C2D;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-wrap {
            width: 100%;
            max-width: 420px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-logo span {
            font-family: 'Cinzel', serif;
            font-size: 1.6rem;
            letter-spacing: 0.3em;
            color: #C9A96E;
        }
        .login-logo p {
            font-size: 0.75rem;
            color: rgba(247,244,238,0.4);
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-top: 0.4rem;
        }
        .login-card {
            background: #0f2035;
            border: 1px solid rgba(201,169,110,0.15);
            border-radius: 16px;
            padding: 2.5rem 2rem;
        }
        .login-card h2 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #F7F4EE;
            margin-bottom: 0.4rem;
        }
        .login-card p {
            font-size: 0.82rem;
            color: rgba(247,244,238,0.45);
            margin-bottom: 1.75rem;
        }
        .form-group {
            margin-bottom: 1.1rem;
        }
        .form-group label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: rgba(247,244,238,0.65);
            letter-spacing: 0.05em;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
        }
        .form-input {
            width: 100%;
            padding: 0.7rem 0.9rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(201,169,110,0.2);
            border-radius: 8px;
            color: #F7F4EE;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #C9A96E;
            background: rgba(255,255,255,0.07);
        }
        .form-input::placeholder { color: rgba(247,244,238,0.25); }
        .input-wrap { position: relative; }
        .toggle-pw {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(247,244,238,0.4);
            cursor: pointer;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            padding: 4px;
        }
        .toggle-pw:hover { color: #C9A96E; }
        .error-msg {
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            color: #fca5a5;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
        }
        .success-msg {
            background: rgba(16,185,129,0.12);
            border: 1px solid rgba(16,185,129,0.3);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            color: #6ee7b7;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
        }
        .login-btn {
            width: 100%;
            padding: 0.85rem;
            background: #C9A96E;
            color: #0B1C2D;
            border: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor: pointer;
            margin-top: 0.75rem;
            transition: background 0.2s, transform 0.1s;
        }
        .login-btn:hover { background: #b8964c; transform: translateY(-1px); }
        .login-btn:active { transform: translateY(0); }
        .login-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .hint-box {
            margin-top: 1.5rem;
            padding: 0.75rem 1rem;
            background: rgba(201,169,110,0.06);
            border: 1px solid rgba(201,169,110,0.1);
            border-radius: 8px;
            font-size: 0.78rem;
            color: rgba(247,244,238,0.4);
        }
        .hint-box strong { color: rgba(201,169,110,0.7); }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: rgba(247,244,238,0.35);
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover { color: #C9A96E; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-logo">
        <span>AUSHVERA</span>
        <p>Admin Panel</p>
    </div>

    <div class="login-card">
        <h2>Welcome back</h2>
        <p>Sign in to your admin account</p>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="error-msg">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Success (e.g. after seeder) --}}
        @if (session('status'))
            <div class="success-msg">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input class="form-input"
                       type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       autocomplete="email"
                       placeholder="admin@aushvera.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <input class="form-input"
                           type="password"
                           id="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           placeholder="••••••••"
                           style="padding-right:3.5rem;">
                    <button type="button" class="toggle-pw" onclick="togglePassword()">SHOW</button>
                </div>
            </div>

            <button type="submit" class="login-btn" id="submitBtn">
                Sign In
            </button>
        </form>

        <div class="hint-box">
            Default admin credentials:<br>
            <strong>Email:</strong> nikunj@superadmin.com<br>
            <strong>Password:</strong> Nikunj@2025!
        </div>
    </div>

    <a href="{{ route('home') }}" class="back-link">← Back to AUSHVERA store</a>
</div>

<script>
function togglePassword() {
    const pw = document.getElementById('password');
    const btn = document.querySelector('.toggle-pw');
    const isHidden = pw.type === 'password';
    pw.type = isHidden ? 'text' : 'password';
    btn.textContent = isHidden ? 'HIDE' : 'SHOW';
}

document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = 'Signing in...';
});
</script>
</body>
</html>
