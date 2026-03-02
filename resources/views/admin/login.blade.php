<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — AUSHVERA</title>
    <script>
        // Unified login — redirect to profile page
        // profile-auth.js detects admin/super_admin role and redirects to /admin automatically
        window.location.replace('/profile?intent=admin-login');
    </script>
    <noscript>
        <meta http-equiv="refresh" content="0;url=/profile">
    </noscript>
</head>
<body>
    <p style="font-family:sans-serif;text-align:center;padding:40px;color:#666;">
        Redirecting to login... <a href="/profile">Click here</a> if not redirected.
    </p>
</body>
</html>
