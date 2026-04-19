/**
 * AUSHVERA AI Chatbot Widget
 * Smart Ayurvedic wellness assistant for users
 */
(function() {
    'use strict';

    // ── Knowledge Base ─────────────────────────────────────
    const KB = {
        greet: [
            "Namaste! 🙏 I'm Veda, your Aushvera wellness guide. How can I help you today?",
            "Welcome to Aushvera! 🌿 I'm Veda, ready to guide you through our Ayurvedic world. What can I do for you?"
        ],
        farewell: ["Thank you for visiting! May your wellness journey be blessed. Namaste 🙏", "Take care and stay well! 🌿"],
        products: [
            "🌿 We offer a curated range of Ayurvedic wellness products — herbal teas, oils, skincare, and ritual essentials. Visit our <a href='/products' style='color:#B8964C'>Products page</a> to explore the full collection!",
            "✨ Our products are crafted with ancient botanical wisdom. Browse all items on our <a href='/products' style='color:#B8964C'>Products page</a>!"
        ],
        shipping: [
            "📦 We ship across India! Standard delivery takes 3-7 business days. Free shipping on orders above ₹999.",
            "🚚 Delivery timelines are typically 3-7 business days. Add items to your cart to see shipping options!"
        ],
        returns: [
            "↩️ We accept returns within 7 days of delivery for unused, sealed products. Contact our support at support@aushvera.com for assistance.",
        ],
        ingredients: [
            "🌱 All Aushvera products use 100% natural, ethically sourced Ayurvedic ingredients — free from harmful chemicals, parabens, and sulfates.",
        ],
        subscription: [
            "⭐ Our subscription plans offer exclusive member discounts, early access to launches, and premium Ayurvedic ritual guides. Log in to your <a href='/profile' style='color:#B8964C'>Profile</a> to subscribe!",
        ],
        ritual: [
            "🕯️ Our Ayurvedic rituals blend ancient wisdom with modern living. Explore the <a href='/ritual' style='color:#B8964C'>Rituals page</a> for detailed guides!",
        ],
        philosophy: [
            "📜 Aushvera's philosophy centers on the intersection of ancient Ayurvedic heritage and refined modern wellness. Read more on our <a href='/philosophy' style='color:#B8964C'>Philosophy page</a>.",
        ],
        contact: [
            "📬 You can reach us at support@aushvera.com or via our <a href='/contact' style='color:#B8964C'>Contact page</a>. We respond within 24 hours!",
        ],
        discount: [
            "🎟️ Use a coupon code at checkout to get discounts! Subscribe to our newsletter or become a member for exclusive offers.",
        ],
        ayurveda: [
            "🌿 Ayurveda is an ancient Indian science of life (5000+ years old) that balances mind, body, and spirit through natural herbs, diet, and lifestyle. At Aushvera, we bring this wisdom to your daily routine!",
        ],
        payment: [
            "💳 We accept UPI, credit/debit cards, net banking, and cash on delivery. All payments are 100% secure.",
        ],
        default: [
            "I'd love to help! Could you tell me more about what you're looking for? I can assist with products, orders, Ayurveda, recipes, or wellness tips. 🌿",
            "That's a great question! For detailed assistance, please visit our <a href='/contact' style='color:#B8964C'>Contact page</a> or browse our <a href='/products' style='color:#B8964C'>Products</a>. 🙏",
            "I'm still learning! For this, please reach out to our team at support@aushvera.com — they'll be happy to help! ✨"
        ]
    };

    const SUGGESTIONS = ['🛍️ Shop Products', '📦 Shipping Info', '🌿 What is Ayurveda?', '⭐ Membership Plans', '📞 Contact Us'];

    // ── Match user input to a category ────────────────────
    function getReply(msg) {
        const t = msg.toLowerCase();
        if (/hi|hello|hey|namaste|good\s*(morning|evening|afternoon)|how are/i.test(t)) return rand(KB.greet);
        if (/bye|goodbye|thank|thanks|farewell/i.test(t)) return rand(KB.farewell);
        if (/product|shop|buy|item|collection|range|catalog/i.test(t)) return rand(KB.products);
        if (/ship|deliver|delivery|dispatch|track|courier/i.test(t)) return rand(KB.shipping);
        if (/return|refund|exchange|cancel.*order/i.test(t)) return rand(KB.returns);
        if (/ingredi|natural|organic|chemical|herb|botanical/i.test(t)) return rand(KB.ingredients);
        if (/subscri|member|plan|premium|gold|benefit/i.test(t)) return rand(KB.subscription);
        if (/ritual|routine|practice|ceremony/i.test(t)) return rand(KB.ritual);
        if (/philosoph|mission|vision|about|story|brand/i.test(t)) return rand(KB.philosophy);
        if (/contact|support|help|email|reach|call/i.test(t)) return rand(KB.contact);
        if (/discount|coupon|offer|promo|code|sale/i.test(t)) return rand(KB.discount);
        if (/ayurved|dosha|vata|pitta|kapha|chakra|herb/i.test(t)) return rand(KB.ayurveda);
        if (/pay|payment|upi|card|cash|wallet/i.test(t)) return rand(KB.payment);
        return rand(KB.default);
    }

    function rand(arr) { return arr[Math.floor(Math.random() * arr.length)]; }

    // ── Build DOM ──────────────────────────────────────────
    function buildWidget() {
        // CSS
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = '/assets/css/chatbot.css';
        document.head.appendChild(link);

        // Floating button
        const btn = document.createElement('button');
        btn.id = 'aushvera-chatbot-btn';
        btn.title = 'Chat with Veda, your wellness guide';
        btn.innerHTML = `<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`;
        document.body.appendChild(btn);

        // Panel
        const panel = document.createElement('div');
        panel.id = 'aushvera-chatbot-panel';
        panel.innerHTML = `
            <div class="chat-panel-header">
                <div class="chat-panel-avatar">🌿</div>
                <div>
                    <div class="chat-panel-name">Veda — Wellness Guide</div>
                    <div class="chat-panel-status">Online & Ready</div>
                </div>
                <button class="chat-close-btn" id="chatCloseBtn">×</button>
            </div>
            <div class="chat-messages-area" id="chatMsgsArea"></div>
            <div class="chat-suggestions" id="chatSuggestions">
                ${SUGGESTIONS.map(s => `<button class="chat-suggestion-btn" onclick="window._aushveraChatSend('${s.replace(/[🛍️📦🌿⭐📞]/gu,'').trim()}')">${s}</button>`).join('')}
            </div>
            <div class="chat-input-area">
                <textarea id="chatBotInput" rows="1" placeholder="Ask about products, wellness, orders..."></textarea>
                <button class="chat-send-btn" id="chatSendBtn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>`;
        document.body.appendChild(panel);

        // Events
        btn.addEventListener('click', () => {
            const isOpen = panel.classList.contains('open');
            panel.classList.toggle('open');
            if (!isOpen) {
                btn.innerHTML = `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`;
                if (!document.querySelector('.chat-msg')) sendGreeting();
            } else {
                btn.innerHTML = `<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`;
            }
        });

        document.getElementById('chatCloseBtn').addEventListener('click', () => {
            panel.classList.remove('open');
            btn.innerHTML = `<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`;
        });

        document.getElementById('chatSendBtn').addEventListener('click', sendMsg);
        document.getElementById('chatBotInput').addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMsg(); }
        });
        document.getElementById('chatBotInput').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 80) + 'px';
        });
    }

    function appendMsg(text, role) {
        const area = document.getElementById('chatMsgsArea');
        const div = document.createElement('div');
        div.className = `chat-msg ${role}`;
        const icon = role === 'bot' ? '🌿' : '👤';
        div.innerHTML = `<div class="chat-msg-icon">${icon}</div><div class="chat-msg-bubble">${text}</div>`;
        area.appendChild(div);
        area.scrollTop = area.scrollHeight;
        return div;
    }

    function showTyping() {
        const area = document.getElementById('chatMsgsArea');
        const div = document.createElement('div');
        div.className = 'chat-msg bot';
        div.id = 'typingIndicator';
        div.innerHTML = `<div class="chat-msg-icon">🌿</div><div class="chat-msg-bubble"><div class="typing-indicator"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div></div>`;
        area.appendChild(div);
        area.scrollTop = area.scrollHeight;
    }

    function hideTyping() {
        document.getElementById('typingIndicator')?.remove();
    }

    function sendGreeting() {
        setTimeout(() => {
            showTyping();
            setTimeout(() => {
                hideTyping();
                appendMsg(rand(KB.greet), 'bot');
            }, 800);
        }, 300);
    }

    function sendMsg() {
        const input = document.getElementById('chatBotInput');
        const msg = input.value.trim();
        if (!msg) return;
        input.value = '';
        input.style.height = 'auto';

        // Hide suggestions after first message
        document.getElementById('chatSuggestions').style.display = 'none';

        appendMsg(escapeHtml(msg), 'user');
        showTyping();

        const delay = 600 + Math.random() * 600;
        setTimeout(() => {
            hideTyping();
            const reply = getReply(msg);
            appendMsg(reply, 'bot');
        }, delay);
    }

    // Expose for suggestion buttons
    window._aushveraChatSend = function(msg) {
        const input = document.getElementById('chatBotInput');
        if (input) { input.value = msg; sendMsg(); }
    };

    function escapeHtml(t) {
        return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function rand(arr) { return arr[Math.floor(Math.random() * arr.length)]; }

    // ── Init ───────────────────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', buildWidget);
    } else {
        buildWidget();
    }
})();
