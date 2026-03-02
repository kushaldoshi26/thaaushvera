@extends('layouts.admin')
@section('title', 'AI Agent')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin-ai.css') }}">
<style>
  body, html { background: #0f0f1a !important; }
  .admin-main { background: #0f0f1a !important; }
  .admin-content { background: transparent !important; padding: 0 !important; }
  .admin-header { background: rgba(255,255,255,.03) !important; border-bottom: 1px solid rgba(255,255,255,.07) !important; }
  .admin-header * { color: rgba(255,255,255,.75) !important; }
</style>
@endpush


@section('content')
<div class="ai-page">

  {{-- Page Header --}}
  <div class="ai-page-header">
    <div class="ai-page-title">
      <div class="ai-title-icon">🤖</div>
      <div>
        <h1>AI Agent</h1>
        <p>Your intelligent business assistant — generate images, content, campaigns & more</p>
      </div>
    </div>
    <div class="ai-status-pill" id="aiStatusPill">
      <span class="status-dot"></span>
      <span id="aiStatusText">Checking...</span>
    </div>
  </div>

  {{-- Tab Bar --}}
  <div class="ai-tabs">
    <button class="ai-tab active" data-tab="overview">🏠 Overview</button>
    <button class="ai-tab" data-tab="banner">🖼️ Banner Generator</button>
    <button class="ai-tab" data-tab="scheme">🎯 Marketing Schemes</button>
    <button class="ai-tab" data-tab="content">✍️ Content Writer</button>
    <button class="ai-tab" data-tab="chat">💬 AI Chat</button>
    <button class="ai-tab" data-tab="usage">📊 Usage</button>
  </div>

  {{-- ============================================================ --}}
  {{-- TAB: OVERVIEW --}}
  {{-- ============================================================ --}}
  <div class="ai-tab-panel active" id="tab-overview">
    <div class="overview-grid">

      <div class="quick-card" onclick="switchTab('banner')">
        <div class="qc-icon">🖼️</div>
        <div class="qc-body">
          <h3>Banner Generator</h3>
          <p>Create stunning promotional banners and hero images for your website with AI</p>
        </div>
        <span class="qc-arrow">→</span>
      </div>

      <div class="quick-card" onclick="switchTab('scheme')">
        <div class="qc-icon">🎯</div>
        <div class="qc-body">
          <h3>Marketing Schemes</h3>
          <p>Get AI-generated seasonal campaigns, discount structures & promotion ideas</p>
        </div>
        <span class="qc-arrow">→</span>
      </div>

      <div class="quick-card" onclick="switchTab('content')">
        <div class="qc-icon">✍️</div>
        <div class="qc-body">
          <h3>Content Writer</h3>
          <p>Write product descriptions, taglines, email campaigns & social media posts</p>
        </div>
        <span class="qc-arrow">→</span>
      </div>

      <div class="quick-card" onclick="switchTab('chat')">
        <div class="qc-icon">💬</div>
        <div class="qc-body">
          <h3>AI Chat</h3>
          <p>Ask anything about your business, pricing strategies, customer insights & more</p>
        </div>
        <span class="qc-arrow">→</span>
      </div>

    </div>

    {{-- Capability Cards --}}
    <div class="capability-section">
      <h2>What Your AI Agent Can Do</h2>
      <div class="capability-grid">
        <div class="cap-item"><span>🖼️</span> Generate banner & product images</div>
        <div class="cap-item"><span>🎯</span> Create seasonal marketing campaigns</div>
        <div class="cap-item"><span>💌</span> Write email newsletters & promotions</div>
        <div class="cap-item"><span>📱</span> Social media captions & hashtags</div>
        <div class="cap-item"><span>📝</span> Product descriptions & taglines</div>
        <div class="cap-item"><span>💰</span> Pricing & discount strategy ideas</div>
        <div class="cap-item"><span>📊</span> Business insights & analytics summaries</div>
        <div class="cap-item"><span>🌿</span> Ayurvedic wellness content writing</div>
        <div class="cap-item"><span>🔍</span> SEO meta descriptions & keywords</div>
        <div class="cap-item"><span>🎨</span> Color palette & style suggestions</div>
        <div class="cap-item"><span>📣</span> Ad copy for Google & Instagram</div>
        <div class="cap-item"><span>🤝</span> Customer engagement scripts</div>
      </div>
    </div>

    {{-- Recent History --}}
    <div class="recent-history-section">
      <h2>Recent AI Activity</h2>
      <div id="overviewHistory" class="history-list-empty">
        <div class="empty-state">
          <span>🤖</span>
          <p>No AI activity yet. Start by generating a banner or writing content!</p>
        </div>
      </div>
    </div>
  </div>

  {{-- ============================================================ --}}
  {{-- TAB: BANNER GENERATOR --}}
  {{-- ============================================================ --}}
  <div class="ai-tab-panel" id="tab-banner">
    <div class="tab-inner">
      <div class="input-panel">
        <h2>🖼️ Banner & Image Generator</h2>
        <p class="panel-subtitle">Describe your vision — AI creates a stunning image for your website, social media, or print.</p>

        <div class="form-group">
          <label>Campaign / Theme</label>
          <input type="text" id="bannerTheme" placeholder="e.g. Monsoon wellness offers, Diwali special, Summer detox..." class="ai-input-field">
        </div>

        <div class="form-group">
          <label>Image Style</label>
          <div class="style-chips" id="bannerStyleChips">
            <button class="chip active" data-val="Luxury minimalist, soft botanical photography">🌿 Botanical</button>
            <button class="chip" data-val="Vibrant festival celebration, golden and warm tones">✨ Festival</button>
            <button class="chip" data-val="Clean white product photography, modern lifestyle">📸 Product</button>
            <button class="chip" data-val="Ayurvedic traditional art, earthy greens and browns">🍃 Ayurvedic</button>
            <button class="chip" data-val="Dark moody luxury wellness, deep purples and golds">🌙 Luxury</button>
            <button class="chip" data-val="Bright summer wellness, fresh greens and yellows">☀️ Summer</button>
          </div>
        </div>

        <div class="form-group">
          <label>Ad Text / Tagline to include (optional)</label>
          <input type="text" id="bannerTagline" placeholder="e.g. Heal from within. Upto 30% off this season." class="ai-input-field">
        </div>

        <div class="form-group">
          <label>Image Size</label>
          <div class="style-chips">
            <button class="chip active" data-group="bannerSize" data-val="1200x628 landscape banner">📺 Banner (1200×628)</button>
            <button class="chip" data-group="bannerSize" data-val="1080x1080 square Instagram post">📷 Instagram Square</button>
            <button class="chip" data-group="bannerSize" data-val="1080x1920 vertical story format">📱 Story (1080×1920)</button>
          </div>
        </div>

        <div class="form-group">
          <label>Full Prompt Preview</label>
          <textarea id="bannerPromptPreview" class="ai-textarea" rows="4" placeholder="Select options above and the prompt will appear here, or type your own..."></textarea>
        </div>

        <button class="ai-run-btn" id="bannerGenBtn" onclick="runBannerGen()">
          <span class="btn-icon">✨</span> Generate Banner Image
        </button>
      </div>

      <div class="result-panel">
        <div class="result-header">
          <h3>Generated Image</h3>
          <button class="result-action-btn" id="bannerDownloadBtn" style="display:none" onclick="downloadResult('bannerResultImg', 'aushvera-banner.png')">⬇️ Download</button>
        </div>
        <div class="result-area" id="bannerResult">
          <div class="result-placeholder">
            <span>🖼️</span>
            <p>Your generated banner will appear here</p>
            <small>Requires AI image service. See "What's Needed" below.</small>
          </div>
        </div>
      </div>
    </div>

    <div class="info-box">
      <h4>📋 What's needed for image generation?</h4>
      <p>Add one of these API keys to your <code>.env</code> file and configure your AI server:</p>
      <ul>
        <li><strong>OpenAI DALL-E 3</strong> — <code>OPENAI_API_KEY=sk-...</code> (~₹3/image, highest quality)</li>
        <li><strong>Stability AI</strong> — <code>STABILITY_API_KEY=sk-...</code> (has free tier)</li>
        <li><strong>Replicate</strong> — <code>REPLICATE_API_TOKEN=r8_...</code> (pay-per-use, many models)</li>
      </ul>
      <p>Without a key, the prompt is generated and shown so you can copy it to any AI tool manually.</p>
    </div>
  </div>

  {{-- ============================================================ --}}
  {{-- TAB: MARKETING SCHEMES --}}
  {{-- ============================================================ --}}
  <div class="ai-tab-panel" id="tab-scheme">
    <div class="tab-inner">
      <div class="input-panel">
        <h2>🎯 Marketing Schemes Generator</h2>
        <p class="panel-subtitle">Get AI-powered campaign plans, seasonal offers, discount strategies & more.</p>

        <div class="form-group">
          <label>Choose a Template (or write your own)</label>
          <div class="template-grid">
            <button class="template-chip" onclick="fillSchemePrompt(this)" data-prompt="Generate a complete Diwali festive marketing campaign for AUSHVERA, an Ayurvedic wellness brand. Include: campaign name, 3 discount offers (% off, BOGO, bundle), 5 social media post ideas, email subject lines, and an estimated timeline.">🪔 Diwali Campaign</button>
            <button class="template-chip" onclick="fillSchemePrompt(this)" data-prompt="Create a Summer Wellness marketing scheme for AUSHVERA. Include detox-focused offers, summer beverage products to highlight, discount tiers for different spend amounts, and 3 Instagram caption ideas.">☀️ Summer Wellness</button>
            <button class="template-chip" onclick="fillSchemePrompt(this)" data-prompt="Design a loyalty program scheme for AUSHVERA customers. Include tier names (Bronze/Silver/Gold), point earn rates, exclusive benefits per tier, and how to promote it via WhatsApp and Email.">⭐ Loyalty Program</button>
            <button class="template-chip" onclick="fillSchemePrompt(this)" data-prompt="Generate a New Year health resolution campaign for AUSHVERA. Include: bundle offer ideas (detox kits, ritual starter packs), a 21-day challenge concept, influencer partnership brief, and 3 ad headlines.">🎊 New Year Campaign</button>
            <button class="template-chip" onclick="fillSchemePrompt(this)" data-prompt="Create a referral marketing scheme for AUSHVERA. Include: referral bonus structure, WhatsApp sharing templates, email templates, how to track referrals, and expected ROI.">🤝 Referral Scheme</button>
            <button class="template-chip" onclick="fillSchemePrompt(this)" data-prompt="Design a flash sale strategy for AUSHVERA for a 48-hour sale event. Include: the best products to discount, pricing psychology (original vs sale price display), countdown timer copy, SMS & push notification text, and urgency messaging.">⚡ Flash Sale Plan</button>
            <button class="template-chip" onclick="fillSchemePrompt(this)" data-prompt="Create a monsoon wellness marketing campaign for AUSHVERA. Focus on immunity booster products, rainy season self-care rituals, and write 5 Instagram post captions, 2 email subject lines, and 3 WhatsApp broadcast messages.">🌧️ Monsoon Campaign</button>
            <button class="template-chip" onclick="fillSchemePrompt(this)" data-prompt="Generate a content calendar for AUSHVERA for the next 30 days. Include daily post themes, best times to post, hashtag suggestions for Instagram, and 1 blog post idea per week aligned with Ayurvedic seasonal wellness.">📅 30-Day Content Calendar</button>
          </div>
        </div>

        <div class="form-group">
          <label>Your Prompt</label>
          <textarea id="schemePrompt" class="ai-textarea" rows="6" placeholder="Describe what campaign, scheme, or strategy you want AI to generate..."></textarea>
        </div>

        <button class="ai-run-btn" id="schemeRunBtn" onclick="runScheme()">
          <span class="btn-icon">🎯</span> Generate Marketing Plan
        </button>
      </div>

      <div class="result-panel">
        <div class="result-header">
          <h3>AI Marketing Plan</h3>
          <button class="result-action-btn" onclick="copyResult('schemeResult')">📋 Copy</button>
        </div>
        <div class="result-area text-result" id="schemeResult">
          <div class="result-placeholder">
            <span>🎯</span>
            <p>Select a template or type your prompt to generate a marketing scheme</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ============================================================ --}}
  {{-- TAB: CONTENT WRITER --}}
  {{-- ============================================================ --}}
  <div class="ai-tab-panel" id="tab-content">
    <div class="tab-inner">
      <div class="input-panel">
        <h2>✍️ Content Writer</h2>
        <p class="panel-subtitle">Generate professional content for your products, emails, social media & website.</p>

        <div class="form-group">
          <label>Content Type</label>
          <div class="style-chips" id="contentTypeChips">
            <button class="chip active" data-val="product description">📦 Product Description</button>
            <button class="chip" data-val="email campaign">💌 Email Campaign</button>
            <button class="chip" data-val="Instagram caption with hashtags">📱 Instagram Post</button>
            <button class="chip" data-val="WhatsApp broadcast message">💬 WhatsApp Message</button>
            <button class="chip" data-val="SEO meta description and keywords">🔍 SEO Content</button>
            <button class="chip" data-val="ad copy for Google Ads">📣 Google Ad Copy</button>
            <button class="chip" data-val="blog article outline">📝 Blog Article</button>
            <button class="chip" data-val="SMS marketing message">📲 SMS Message</button>
          </div>
        </div>

        <div class="form-group">
          <label>Product / Topic</label>
          <input type="text" id="contentTopic" placeholder="e.g. Ashvattha™ Herbal Tea, Immunity Booster Drops, Wellness Ritual Kit..." class="ai-input-field">
        </div>

        <div class="form-group">
          <label>Key Benefits / Points to Highlight</label>
          <input type="text" id="contentBenefits" placeholder="e.g. All-natural, Ayurvedic herbs, calming, supports digestion..." class="ai-input-field">
        </div>

        <div class="form-group">
          <label>Tone</label>
          <div class="style-chips" id="contentToneChips">
            <button class="chip active" data-val="luxury and premium">✨ Premium</button>
            <button class="chip" data-val="warm and friendly">🤗 Friendly</button>
            <button class="chip" data-val="scientific and authoritative">🔬 Scientific</button>
            <button class="chip" data-val="spiritual and holistic">🌿 Holistic</button>
          </div>
        </div>

        <button class="ai-run-btn" onclick="runContent()">
          <span class="btn-icon">✍️</span> Generate Content
        </button>
      </div>

      <div class="result-panel">
        <div class="result-header">
          <h3>Generated Content</h3>
          <button class="result-action-btn" onclick="copyResult('contentResult')">📋 Copy</button>
        </div>
        <div class="result-area text-result" id="contentResult">
          <div class="result-placeholder">
            <span>✍️</span>
            <p>Select content type, fill in details, and click Generate</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ============================================================ --}}
  {{-- TAB: AI CHAT --}}
  {{-- ============================================================ --}}
  <div class="ai-tab-panel" id="tab-chat">
    <div class="chat-container">
      <div class="chat-messages" id="chatMessages">
        <div class="chat-bubble ai-bubble">
          <div class="bubble-avatar">🤖</div>
          <div class="bubble-content">
            <p>Hello! I'm your AUSHVERA AI business assistant. Ask me anything — pricing strategy, product ideas, customer insights, marketing advice, or just brainstorm with me!</p>
          </div>
        </div>
      </div>

      <div class="chat-suggestions">
        <button class="suggestion-chip" onclick="sendChatSuggestion(this)">💰 Best pricing strategy for herbal teas</button>
        <button class="suggestion-chip" onclick="sendChatSuggestion(this)">📊 How to increase repeat customers</button>
        <button class="suggestion-chip" onclick="sendChatSuggestion(this)">🌿 New Ayurvedic product ideas</button>
        <button class="suggestion-chip" onclick="sendChatSuggestion(this)">📣 Best channels to market wellness products in India</button>
      </div>

      <div class="chat-input-bar">
        <textarea id="chatInput" class="chat-textarea" placeholder="Ask your AI assistant anything..." rows="1" onkeydown="handleChatKey(event)"></textarea>
        <button class="chat-send-btn" onclick="sendChat()">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
      </div>
    </div>
  </div>

  {{-- ============================================================ --}}
  {{-- TAB: USAGE --}}
  {{-- ============================================================ --}}
  <div class="ai-tab-panel" id="tab-usage">
    <div class="usage-grid">
      <div class="usage-card">
        <div class="usage-icon">⚡</div>
        <div class="usage-num" id="usageCount">0</div>
        <div class="usage-label">AI Calls This Session</div>
      </div>
      <div class="usage-card">
        <div class="usage-icon">🖼️</div>
        <div class="usage-num" id="imageCount">0</div>
        <div class="usage-label">Images Generated</div>
      </div>
      <div class="usage-card">
        <div class="usage-icon">📝</div>
        <div class="usage-num" id="textCount">0</div>
        <div class="usage-label">Text Outputs</div>
      </div>
    </div>

    <div class="history-section">
      <div class="history-header">
        <h3>Session History</h3>
        <button class="result-action-btn" onclick="clearHistory()">🗑️ Clear History</button>
      </div>
      <div id="usageHistoryList">
        <div class="result-placeholder" style="padding:40px;text-align:center">
          <span>📊</span>
          <p>No requests yet this session.</p>
        </div>
      </div>
    </div>

    <div class="api-setup-section">
      <h3>🔑 API Configuration Guide</h3>
      <p>To enable real AI responses and image generation, add to your <code>.env</code> file:</p>

      <div class="code-block">
        <pre># AI Server URL (run your Python/FastAPI AI server here)
AI_SERVER_URL=http://127.0.0.1:9000

# Option A: OpenAI (DALL-E 3 images + GPT-4 text)
OPENAI_API_KEY=sk-your-key-here

# Option B: Stability AI (image generation)
STABILITY_API_KEY=sk-your-stability-key

# Option C: Google Gemini (text AI)
GEMINI_API_KEY=your-gemini-key</pre>
        <button class="result-action-btn" onclick="copyCode()">📋 Copy</button>
      </div>

      <div class="setup-steps">
        <h4>Quick Setup Steps:</h4>
        <ol>
          <li>Get an API key from <strong>OpenAI</strong> (platform.openai.com) or <strong>Google AI Studio</strong> (aistudio.google.com) — both have free credits</li>
          <li>Add the key to your <code>.env</code> file</li>
          <li>Run <code>php artisan config:clear</code></li>
          <li>Start your Python AI server on port 9000 (or update <code>AI_SERVER_URL</code>)</li>
          <li>Come back here and start generating! 🚀</li>
        </ol>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/admin-ai.js') }}"></script>
@endpush
