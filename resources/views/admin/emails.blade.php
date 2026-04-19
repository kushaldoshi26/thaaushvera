@extends('layouts.admin')
@section('title', 'Email Campaigns')

@section('content')
<div class="page-bar">
    <div class="page-bar-title">📧 Email Campaigns</div>
    <div style="font-size:13px;color:var(--admin-muted);">Send promotional emails to users & subscribers</div>
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:24px;align-items:start;">
    {{-- Left: Compose Form --}}
    <div class="card">
        <div style="padding:24px;border-bottom:1px solid rgba(255,255,255,0.07);">
            <h3 style="margin:0;font-size:15px;font-weight:600;">Compose Email</h3>
        </div>
        <div style="padding:24px;">
            <div class="admin-form-group">
                <label>Recipients</label>
                <select id="emailTarget" class="admin-input">
                    <option value="all">All Registered Users</option>
                    <option value="subscribers">Active Subscribers Only</option>
                    <option value="specific">Specific Emails</option>
                </select>
            </div>

            <div id="specificEmailsGroup" class="admin-form-group" style="display:none;">
                <label>Email Addresses (one per line)</label>
                <textarea id="specificEmails" class="admin-input admin-textarea" rows="4" placeholder="user@example.com&#10;another@example.com"></textarea>
            </div>

            <div class="admin-form-group">
                <label>Email Subject <span style="color:#ef4444">*</span></label>
                <input type="text" id="emailSubject" class="admin-input" placeholder="🌿 Exclusive Wellness Offer Just for You">
            </div>

            <div class="admin-form-group">
                <label>Email Headline <span style="color:#ef4444">*</span></label>
                <input type="text" id="emailHeadline" class="admin-input" placeholder="Discover the Ancient Art of Wellness">
            </div>

            <div class="admin-form-group">
                <label>Email Body <span style="color:#ef4444">*</span></label>
                <textarea id="emailBody" class="admin-input admin-textarea" rows="5" placeholder="Write your message here..."></textarea>
            </div>

            <div class="grid-2">
                <div class="admin-form-group">
                    <label>Coupon Code (optional)</label>
                    <input type="text" id="couponCode" class="admin-input" placeholder="WELLNESS20">
                </div>
                <div class="admin-form-group">
                    <label>Discount % (optional)</label>
                    <input type="number" id="discountPct" class="admin-input" placeholder="20" min="1" max="100">
                </div>
            </div>

            <div class="grid-2">
                <div class="admin-form-group">
                    <label>CTA Button Text</label>
                    <input type="text" id="ctaText" class="admin-input" placeholder="Shop Now" value="Shop Now">
                </div>
                <div class="admin-form-group">
                    <label>CTA Link</label>
                    <input type="text" id="ctaUrl" class="admin-input" placeholder="{{ config('app.url') }}/products" value="{{ config('app.url') }}/products">
                </div>
            </div>

            <div style="margin-top:8px;display:flex;gap:12px;">
                <button id="emailPreviewBtn" onclick="previewEmail()" style="padding:10px 20px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);color:#fff;border-radius:7px;cursor:pointer;font-size:13px;">
                    👁️ Preview
                </button>
                <button id="emailSendBtn" onclick="sendCampaign()" style="flex:1;padding:10px 20px;background:linear-gradient(135deg,#B8964C,#d4a85a);color:#0b1c2d;border:none;border-radius:7px;cursor:pointer;font-size:14px;font-weight:700;">
                    🚀 Send Email Campaign
                </button>
            </div>

            <div id="sendResult" style="margin-top:16px;display:none;"></div>
        </div>
    </div>

    {{-- Right: Templates + Stats --}}
    <div style="display:flex;flex-direction:column;gap:16px;">
        {{-- Quick Templates --}}
        <div class="card">
            <div style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,0.07);font-size:14px;font-weight:600;">⚡ Quick Templates</div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:8px;">
                <button onclick="fillTemplate('sale')" style="text-align:left;background:rgba(184,150,76,0.08);border:1px solid rgba(184,150,76,0.2);color:#B8964C;padding:10px 14px;border-radius:8px;cursor:pointer;font-size:13px;">
                    🎊 Festival Sale Announcement
                </button>
                <button onclick="fillTemplate('new_product')" style="text-align:left;background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);color:#60a5fa;padding:10px 14px;border-radius:8px;cursor:pointer;font-size:13px;">
                    ✨ New Product Launch
                </button>
                <button onclick="fillTemplate('welcome')" style="text-align:left;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);color:#10b981;padding:10px 14px;border-radius:8px;cursor:pointer;font-size:13px;">
                    🌿 Welcome / Thank You
                </button>
                <button onclick="fillTemplate('coupon')" style="text-align:left;background:rgba(139,92,246,0.08);border:1px solid rgba(139,92,246,0.2);color:#a78bfa;padding:10px 14px;border-radius:8px;cursor:pointer;font-size:13px;">
                    🎟️ Exclusive Coupon Offer
                </button>
                <button onclick="fillTemplate('seasonal')" style="text-align:left;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);color:#f87171;padding:10px 14px;border-radius:8px;cursor:pointer;font-size:13px;">
                    🌸 Seasonal Wellness Tips
                </button>
            </div>
        </div>

        {{-- Send Stats --}}
        <div class="card" id="statsCard" style="display:none;">
            <div style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,0.07);font-size:14px;font-weight:600;">📊 Last Campaign Result</div>
            <div id="statsContent" style="padding:16px;"></div>
        </div>

        {{-- Preview Panel --}}
        <div class="card" id="previewCard" style="display:none;">
            <div style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,0.07);font-size:14px;font-weight:600;">👁️ Email Preview</div>
            <div id="previewContent" style="padding:16px;font-size:13px;color:var(--admin-muted);"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const token  = api.getToken();
const headers = { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' };

// Toggle specific email textarea
document.getElementById('emailTarget').addEventListener('change', function() {
    document.getElementById('specificEmailsGroup').style.display = this.value === 'specific' ? '' : 'none';
});

// Templates
const TEMPLATES = {
    sale: {
        subject: '🎊 Festival Sale — Up to 40% OFF on Aushvera Wellness Products!',
        headline: 'Celebrate With Wellness',
        body: 'Dear Wellness Seeker,\n\nThis festive season, we bring you an exclusive offer on our entire range of Ayurvedic wellness products. Each product is crafted with ancient botanical wisdom to bring balance to your mind, body, and spirit.\n\nUse the coupon code below to unlock your discount!'
    },
    new_product: {
        subject: '✨ New Launch: Discover Our Latest Ayurvedic Creation',
        headline: 'Something New Has Arrived',
        body: 'Dear Wellness Enthusiast,\n\nWe are thrilled to introduce our latest addition to the Aushvera family. Crafted with the finest natural ingredients and time-honored Ayurvedic formulas, this product is designed to elevate your wellness journey.\n\nBe among the first to experience it!'
    },
    welcome: {
        subject: '🌿 Welcome to the Aushvera Wellness Family!',
        headline: 'Welcome to Your Wellness Journey',
        body: 'Dear Friend,\n\nThank you for choosing Aushvera. You have taken the first step toward a life rooted in natural wellness and Ayurvedic wisdom.\n\nAs a valued member of our community, you enjoy exclusive access to our premium wellness products, early product launches, and member-only offers.'
    },
    coupon: {
        subject: '🎟️ Your Exclusive Discount Coupon Inside — AUSHVERA',
        headline: 'A Gift From Us to You',
        body: 'Dear Valued Customer,\n\nAs a token of our appreciation, we\'re sending you an exclusive discount coupon. This is our way of saying thank you for being a part of the Aushvera wellness community.\n\nUse the coupon below on your next purchase!'
    },
    seasonal: {
        subject: '🌸 Seasonal Wellness Guide — Ayurveda for This Season',
        headline: 'Align Your Wellness With the Seasons',
        body: 'Dear Wellness Seeker,\n\nAyurveda teaches us that our bodies need different care with each changing season. This time of year calls for special attention to your routine.\n\nExplore our curated selection of products perfectly suited for seasonal wellbeing.'
    }
};

function fillTemplate(type) {
    const t = TEMPLATES[type];
    document.getElementById('emailSubject').value  = t.subject;
    document.getElementById('emailHeadline').value = t.headline;
    document.getElementById('emailBody').value     = t.body;
    if (type === 'coupon' || type === 'sale') {
        document.getElementById('couponCode').value  = type === 'sale' ? 'FESTIVAL40' : 'THANKYOU15';
        document.getElementById('discountPct').value = type === 'sale' ? 40 : 15;
    }
}

function previewEmail() {
    const subject  = document.getElementById('emailSubject').value || 'Your Subject';
    const headline = document.getElementById('emailHeadline').value || 'Your Headline';
    const body     = document.getElementById('emailBody').value || '...';
    const coupon   = document.getElementById('couponCode').value;
    const discount = document.getElementById('discountPct').value;

    let html = `
        <div style="background:#0d1f35;border-radius:10px;padding:20px;color:#f7f4ee;">
            <div style="color:#B8964C;font-size:10px;letter-spacing:3px;margin-bottom:8px;">SUBJECT: ${subject}</div>
            <div style="font-size:18px;font-weight:700;margin-bottom:12px;">${headline}</div>
            <div style="font-size:13px;color:rgba(247,244,238,0.7);line-height:1.7;white-space:pre-wrap;">${body}</div>
            ${coupon ? `<div style="border:2px dashed #B8964C;border-radius:8px;padding:12px;text-align:center;margin-top:16px;">
                <div style="font-size:10px;color:#9ca3af;margin-bottom:4px;">COUPON CODE</div>
                <div style="font-size:22px;letter-spacing:6px;color:#B8964C;font-weight:bold;font-family:monospace;">${coupon}</div>
                ${discount ? `<div style="font-size:11px;color:#10b981;margin-top:4px;">${discount}% OFF</div>` : ''}
            </div>` : ''}
        </div>`;

    document.getElementById('previewContent').innerHTML = html;
    document.getElementById('previewCard').style.display = '';
}

async function sendCampaign() {
    const target   = document.getElementById('emailTarget').value;
    const subject  = document.getElementById('emailSubject').value.trim();
    const headline = document.getElementById('emailHeadline').value.trim();
    const body     = document.getElementById('emailBody').value.trim();

    if (!subject || !headline || !body) {
        alert('Please fill in Subject, Headline, and Body before sending.');
        return;
    }

    const btn = document.getElementById('emailSendBtn');
    btn.disabled = true; btn.textContent = '⏳ Sending...';

    const payload = {
        target,
        subject,
        headline,
        body,
        coupon_code: document.getElementById('couponCode').value || null,
        discount:    parseFloat(document.getElementById('discountPct').value) || null,
        cta_text:    document.getElementById('ctaText').value || 'Shop Now',
        cta_url:     document.getElementById('ctaUrl').value || '',
        emails:      target === 'specific' ? document.getElementById('specificEmails').value.split('\n').map(e => e.trim()).filter(Boolean) : [],
    };

    try {
        const res  = await fetch('/api/admin/email/send-promo', { method: 'POST', headers, body: JSON.stringify(payload) });
        const json = await res.json();
        const result = document.getElementById('sendResult');
        result.style.display = '';
        if (json.success) {
            result.innerHTML = `<div style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);border-radius:8px;padding:16px;color:#10b981;">
                ✅ <strong>Campaign Sent!</strong> ${json.message}</div>`;
            document.getElementById('statsCard').style.display = '';
            document.getElementById('statsContent').innerHTML = `
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="background:rgba(16,185,129,0.1);border-radius:8px;padding:12px;text-align:center;">
                        <div style="font-size:24px;font-weight:700;color:#10b981;">${json.sent || 0}</div>
                        <div style="font-size:11px;color:var(--admin-muted);">Delivered</div>
                    </div>
                    <div style="background:rgba(239,68,68,0.1);border-radius:8px;padding:12px;text-align:center;">
                        <div style="font-size:24px;font-weight:700;color:#ef4444;">${json.failed || 0}</div>
                        <div style="font-size:11px;color:var(--admin-muted);">Failed</div>
                    </div>
                </div>`;
        } else {
            result.innerHTML = `<div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:8px;padding:16px;color:#ef4444;">
                ❌ ${json.message || 'Failed to send campaign.'}</div>`;
        }
    } catch(e) {
        document.getElementById('sendResult').style.display = '';
        document.getElementById('sendResult').innerHTML = `<div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:8px;padding:16px;color:#ef4444;">⚠️ Network error</div>`;
    }
    btn.disabled = false; btn.textContent = '🚀 Send Email Campaign';
}
</script>
@endpush
