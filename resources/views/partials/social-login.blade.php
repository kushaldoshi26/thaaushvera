<div class="social-login-container">
    <div class="divider">
        <span>Or continue with</span>
    </div>
    
    <div class="social-login-buttons">
        <a href="{{ route('auth.social', 'google') }}" class="social-btn google-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.70 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.60 3.30-4.53 6.16-4.53z"/>
            </svg>
            Google
        </a>
        
        <a href="{{ route('auth.social', 'facebook') }}" class="social-btn facebook-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            Facebook
        </a>
    </div>
</div>

<style>
.social-login-container {
    margin-top: 24px;
}

.divider {
    position: relative;
    text-align: center;
    margin: 20px 0;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #e5e7eb;
}

.divider span {
    background: #fff;
    padding: 0 12px;
    position: relative;
    color: #6b7280;
    font-size: 14px;
}

.social-login-buttons {
    display: flex;
    gap: 12px;
}

.social-btn {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background: #fff;
    color: #1f2937;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 14px;
}

.social-btn:hover {
    border-color: #d1d5db;
    background: #f9fafb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.social-btn svg {
    color: #B8964C; /* golden icons */
}

.google-btn:active {
    background: #f3f4f6;
}

.facebook-btn:active {
    background: #f3f4f6;
}

@media (max-width: 640px) {
    .social-login-buttons {
        flex-direction: column;
    }

    .social-btn {
        width: 100%;
    }
}
</style>