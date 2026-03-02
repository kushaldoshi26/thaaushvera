<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — AUSHVERA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo h1 {
            color: #c9a96e;
            font-size: 28px;
            letter-spacing: 4px;
            font-weight: 300;
        }
        .logo p {
            color: rgba(255,255,255,0.4);
            font-size: 12px;
            letter-spacing: 2px;
            margin-top: 4px;
            text-transform: uppercase;
        }
        .alert-error {
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        input {
            width: 100%;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            padding: 14px 16px;
            color: #fff;
            font-size: 15px;
            outline: none;
            transition: border-color 0.2s;
        }
        input:focus {
            border-color: #c9a96e;
            background: rgba(201,169,110,0.06);
        }
        input::placeholder { color: rgba(255,255,255,0.25); }
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #c9a96e, #a8823c);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s;
            margin-top: 8px;
        }
        .btn-login:hover { opacity: 0.9; }
        .btn-login:active { transform: scale(0.98); }
        .back-link {
            text-align: center;
            margin-top: 24px;
        }
        .back-link a {
            color: rgba(255,255,255,0.35);
            font-size: 13px;
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link a:hover { color: #c9a96e; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <h1>AUSHVERA</h1>
            <p>Admin Panel</p>
        </div>

        @if($errors->any())
            <div class="alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@aushvera.com" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn-login">Sign In to Admin Panel</button>
        </form>

        <div class="back-link">
            <a href="{{ url('/') }}">← Back to Website</a>
        </div>
    </div>
</body>
</html>
