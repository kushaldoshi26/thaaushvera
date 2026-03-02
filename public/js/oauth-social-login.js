// oauth-social-login.js
// JavaScript helper functions for social auth flows (Google & Facebook).

async function loginWithGoogle() {
    try {
        if (typeof google !== 'undefined' && google.accounts) {
            google.accounts.id.initialize({
                client_id: document.body.dataset.googleClientId || 'YOUR_GOOGLE_CLIENT_ID',
                callback: handleGoogleCallback
            });
            google.accounts.id.renderButton(
                document.querySelector('[data-google-signin]'),
                { theme: "outline", size: "large" }
            );
        } else {
            window.location.href = `/oauth/google?redirect_uri=${encodeURIComponent(window.location.href)}`;
        }
    } catch (error) {
        console.error('Google login error:', error);
        alert('Failed to initialize Google login. Please try again.');
    }
}

async function loginWithFacebook() {
    try {
        if (typeof FB !== 'undefined') {
            FB.login(handleFacebookCallback, { scope: 'public_profile,email' });
        } else {
            window.location.href = `/oauth/facebook?redirect_uri=${encodeURIComponent(window.location.href)}`;
        }
    } catch (error) {
        console.error('Facebook login error:', error);
        alert('Failed to initialize Facebook login. Please try again.');
    }
}

async function handleGoogleCallback(response) {
    try {
        const result = await fetch('http://127.0.0.1:8000/api/oauth/callback', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                token: response.credential,
                provider: 'google'
            })
        });

        const data = await result.json();
        if (data.success && data.token) {
            localStorage.setItem('token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));
            window.location.href = '/';
        } else {
            alert(data.message || 'Login failed');
        }
    } catch (error) {
        console.error('Google callback error:', error);
        alert('Login failed. Please try again.');
    }
}

async function handleFacebookCallback(response) {
    if (response.authResponse) {
        try {
            const result = await fetch('http://127.0.0.1:8000/api/oauth/callback', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    token: response.authResponse.accessToken,
                    provider: 'facebook'
                })
            });

            const data = await result.json();
            if (data.success && data.token) {
                localStorage.setItem('token', data.token);
                localStorage.setItem('user', JSON.stringify(data.user));
                window.location.href = '/';
            } else {
                alert(data.message || 'Login failed');
            }
        } catch (error) {
            console.error('Facebook callback error:', error);
            alert('Login failed. Please try again.');
        }
    } else {
        alert('Facebook login cancelled');
    }
}

// optionally include external SDKs in page:
// <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0" onload="FB.XFBML.parse()"></script>
// <script src="https://accounts.google.com/gsi/client" async defer></script>