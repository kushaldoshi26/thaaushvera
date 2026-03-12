/**
 * AUSHVERA Admin AI Agent — Full JS
 */

/* ================================================================
   GLOBAL STATE
   ================================================================ */
const AI_STATE = { calls: 0, images: 0, texts: 0, history: [] };
const CSRF = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

/* ================================================================
   TAB SWITCHING
   ================================================================ */
function switchTab(name) {
  document.querySelectorAll('.ai-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.ai-tab-panel').forEach(p => p.classList.remove('active'));
  const tab = document.querySelector(`.ai-tab[data-tab="${name}"]`);
  const panel = document.getElementById(`tab-${name}`);
  if (tab) tab.classList.add('active');
  if (panel) panel.classList.add('active');
}

document.querySelectorAll('.ai-tab').forEach(btn => {
  btn.addEventListener('click', () => switchTab(btn.dataset.tab));
});

/* ================================================================
   CHIP GROUPS — single-select
   ================================================================ */
function setupChipGroup(groupId) {
  const group = document.getElementById(groupId);
  if (!group) return;
  group.querySelectorAll('.chip').forEach(chip => {
    chip.addEventListener('click', () => {
      group.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      if (groupId === 'bannerStyleChips' || groupId === 'bannerSize') updateBannerPrompt();
    });
  });
}
setupChipGroup('bannerStyleChips');
setupChipGroup('contentTypeChips');
setupChipGroup('contentToneChips');

// Banner size chips (not using ID — use data-group)
document.querySelectorAll('.chip[data-group="bannerSize"]').forEach(chip => {
  chip.addEventListener('click', () => {
    document.querySelectorAll('.chip[data-group="bannerSize"]').forEach(c => c.classList.remove('active'));
    chip.classList.add('active');
    updateBannerPrompt();
  });
});

/* ================================================================
   AI SERVER STATUS CHECK
   ================================================================ */
async function checkAiStatus() {
  const pill = document.getElementById('aiStatusPill');
  const dot = pill?.querySelector('.status-dot');
  const txt = document.getElementById('aiStatusText');
  try {
    const res = await fetch('/admin/ai-agent', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF() },
      body: JSON.stringify({ message: 'ping', mode: 'ping' }),
      signal: AbortSignal.timeout(4000)
    });
    const data = await res.json();
    if (data.error && data.error.includes('connection')) throw new Error('offline');
    dot?.classList.add('online');
    if (txt) txt.textContent = 'AI Online';
  } catch {
    dot?.classList.add('offline');
    if (dot) dot.classList.remove('online');
    if (txt) txt.textContent = 'AI Server Offline';
  }
}
checkAiStatus();

/* ================================================================
   CORE: POST TO AI AGENT
   ================================================================ */
async function callAi(payload) {
  const res = await fetch('/admin/ai-agent', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF() },
    body: JSON.stringify(payload)
  });
  return await res.json();
}

/* ================================================================
   HISTORY TRACKING
   ================================================================ */
function addHistory(mode, prompt, type = 'text') {
  AI_STATE.calls++;
  if (type === 'image') AI_STATE.images++;
  else AI_STATE.texts++;

  const entry = { mode, prompt: prompt.substring(0, 120), type, time: new Date().toLocaleTimeString() };
  AI_STATE.history.unshift(entry);
  renderHistory();
  updateUsageCounts();
}

function renderHistory() {
  const list = document.getElementById('usageHistoryList');
  const overviewList = document.getElementById('overviewHistory');
  if (!list) return;

  if (AI_STATE.history.length === 0) {
    list.innerHTML = `<div class="result-placeholder" style="padding:40px;text-align:center"><span>📊</span><p>No requests yet this session.</p></div>`;
    if (overviewList) overviewList.innerHTML = `<div class="history-list-empty"><div class="empty-state"><span>🤖</span><p>No AI activity yet.</p></div></div>`;
    return;
  }

  const html = AI_STATE.history.slice(0, 30).map(e => `
    <div class="history-entry">
      <div class="he-mode">${e.mode} · ${e.type}</div>
      <div class="he-prompt">${escHtml(e.prompt)}</div>
      <div class="he-time">${e.time}</div>
    </div>`).join('');
  list.innerHTML = html;

  if (overviewList) {
    overviewList.innerHTML = `<div style="display:flex;flex-direction:column;gap:10px">${html.slice(0, 3 * 200)}</div>`;
  }
}

function updateUsageCounts() {
  const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
  setEl('usageCount', AI_STATE.calls);
  setEl('imageCount', AI_STATE.images);
  setEl('textCount', AI_STATE.texts);
}

function clearHistory() {
  AI_STATE.history = [];
  AI_STATE.calls = 0; AI_STATE.images = 0; AI_STATE.texts = 0;
  renderHistory();
  updateUsageCounts();
}

/* ================================================================
   BANNER GENERATOR
   ================================================================ */
function updateBannerPrompt() {
  const theme = document.getElementById('bannerTheme')?.value || '';
  const style = document.querySelector('#bannerStyleChips .chip.active')?.dataset.val || 'Luxury minimalist botanical photography';
  const size = document.querySelector('.chip[data-group="bannerSize"].active')?.dataset.val || '1200x628 landscape banner';
  const tagline = document.getElementById('bannerTagline')?.value || '';

  let prompt = `${style} image for AUSHVERA, an Ayurvedic wellness brand.`;
  if (theme) prompt += ` Theme: ${theme}.`;
  if (tagline) prompt += ` Include elegant text overlay: "${tagline}".`;
  prompt += ` Format: ${size}. Photorealistic, high-quality, professional.`;

  const preview = document.getElementById('bannerPromptPreview');
  if (preview) preview.value = prompt;
}

document.getElementById('bannerTheme')?.addEventListener('input', updateBannerPrompt);
document.getElementById('bannerTagline')?.addEventListener('input', updateBannerPrompt);

async function runBannerGen() {
  const prompt = document.getElementById('bannerPromptPreview')?.value?.trim();
  if (!prompt) { updateBannerPrompt(); }
  const finalPrompt = document.getElementById('bannerPromptPreview')?.value?.trim();
  if (!finalPrompt) { alert('Please fill in the theme first.'); return; }

  const btn = document.getElementById('bannerGenBtn');
  const resultEl = document.getElementById('bannerResult');
  setLoading(btn, resultEl, true);

  try {
    const data = await callAi({ message: finalPrompt, mode: 'image' });
    addHistory('Banner Generator', finalPrompt, 'image');

    if (data.saved_image_path || data.image_url) {
      const url = data.saved_image_path || data.image_url;
      resultEl.innerHTML = `<img src="${url}" class="result-image" id="bannerResultImg" alt="Generated banner">`;
      const dlBtn = document.getElementById('bannerDownloadBtn');
      if (dlBtn) dlBtn.style.display = '';
    } else if (data.content) {
      resultEl.innerHTML = `<div class="ai-success-msg" style="margin:20px"><strong>Image Prompt Generated:</strong><br><br>${escHtml(data.content)}<br><br><em>Copy the prompt above and paste it into DALL-E or Midjourney to generate the image.</em></div>`;
    } else if (data.error) {
      resultEl.innerHTML = `<div class="ai-error-msg" style="margin:20px">⚠️ ${escHtml(data.error || 'AI server not connected.')}<br><br><strong>Your prompt is ready to use manually:</strong><br><em>${escHtml(finalPrompt)}</em></div>`;
    } else {
      resultEl.innerHTML = `<div class="ai-success-msg" style="margin:20px">Prompt ready: <br><em>${escHtml(finalPrompt)}</em></div>`;
    }
  } catch (err) {
    resultEl.innerHTML = `<div class="ai-error-msg" style="margin:20px">⚠️ Could not reach AI server.<br><br><strong>Your prompt:</strong><br><em>${escHtml(finalPrompt)}</em><br><br>Copy this prompt and use it in any AI image tool (DALL-E, Midjourney, Canva AI, etc.)</div>`;
  }
  setLoading(btn, null, false);
}

/* ================================================================
   MARKETING SCHEMES
   ================================================================ */
function fillSchemePrompt(btn) {
  const prompt = btn.dataset.prompt;
  const el = document.getElementById('schemePrompt');
  if (el) { el.value = prompt; el.focus(); }
}

async function runScheme() {
  const prompt = document.getElementById('schemePrompt')?.value?.trim();
  if (!prompt) { alert('Please enter a prompt or select a template.'); return; }

  const btn = document.getElementById('schemeRunBtn');
  const resultEl = document.getElementById('schemeResult');
  setLoading(btn, resultEl, true, true);

  try {
    const data = await callAi({ message: prompt, mode: 'scheme' });
    addHistory('Marketing Scheme', prompt, 'text');
    const text = data.content || data.message || data.error || JSON.stringify(data, null, 2);
    resultEl.innerHTML = `<div style="white-space:pre-wrap;line-height:1.8">${renderMarkdownBasic(text)}</div>`;
    if (data.error) resultEl.innerHTML += `<div class="ai-error-msg" style="margin-top:12px">⚠️ AI server offline. Response may be limited.</div>`;
  } catch {
    resultEl.innerHTML = `<div class="ai-error-msg">⚠️ AI server not reachable. Please check the Usage tab for setup instructions.</div>`;
  }
  setLoading(btn, null, false);
}

/* ================================================================
   CONTENT WRITER
   ================================================================ */
async function runContent() {
  const type = document.querySelector('#contentTypeChips .chip.active')?.dataset.val || 'product description';
  const topic = document.getElementById('contentTopic')?.value?.trim();
  const benefits = document.getElementById('contentBenefits')?.value?.trim();
  const tone = document.querySelector('#contentToneChips .chip.active')?.dataset.val || 'luxury and premium';

  if (!topic) { alert('Please enter a product or topic.'); return; }

  const prompt = `Write a ${type} for AUSHVERA (an Ayurvedic wellness brand) for: ${topic}.${benefits ? ` Key benefits: ${benefits}.` : ''} Tone: ${tone}. Brand values: natural, authentic, Ayurvedic heritage, premium.`;

  const resultEl = document.getElementById('contentResult');
  setLoading(null, resultEl, true, true);

  try {
    const data = await callAi({ message: prompt, mode: 'content' });
    addHistory('Content Writer', prompt, 'text');
    const text = data.content || data.message || data.error || JSON.stringify(data, null, 2);
    resultEl.innerHTML = `<div style="white-space:pre-wrap;line-height:1.8">${renderMarkdownBasic(text)}</div>`;
  } catch {
    resultEl.innerHTML = `<div class="ai-error-msg">⚠️ AI server not reachable. Please check the Usage tab for setup instructions.</div>`;
  }
}

/* ================================================================
   AI CHAT
   ================================================================ */
const chatHistory = [];

async function sendChat() {
  const input = document.getElementById('chatInput');
  const msg = input?.value?.trim();
  if (!msg) return;
  input.value = '';
  input.style.height = 'auto';

  appendChatBubble(msg, 'user');
  chatHistory.push({ role: 'user', content: msg });

  const typingId = appendChatBubble('...', 'ai', true);

  try {
    const data = await callAi({ message: msg, mode: 'chat', history: chatHistory.slice(-10) });
    addHistory('Chat', msg, 'text');
    const reply = data.content || data.message || data.error || 'I could not connect to the AI server. Please check setup in the Usage tab.';
    removeBubble(typingId);
    appendChatBubble(reply, 'ai');
    chatHistory.push({ role: 'assistant', content: reply });
  } catch {
    removeBubble(typingId);
    appendChatBubble('⚠️ AI server offline. Please see the Usage tab for setup instructions.', 'ai');
  }
}

function sendChatSuggestion(btn) {
  const input = document.getElementById('chatInput');
  if (input) { input.value = btn.textContent.replace(/^[\p{Emoji}\s]+/u, '').trim(); sendChat(); }
}

function handleChatKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendChat(); }
}

let bubbleId = 0;
function appendChatBubble(text, role, isTyping = false) {
  const id = `bubble-${++bubbleId}`;
  const messages = document.getElementById('chatMessages');
  const avatar = role === 'ai' ? '🤖' : '👤';
  const cls = role === 'ai' ? 'ai-bubble' : 'user-bubble';
  const bubble = document.createElement('div');
  bubble.className = `chat-bubble ${cls}`;
  bubble.id = id;
  bubble.innerHTML = `
    <div class="bubble-avatar">${avatar}</div>
    <div class="bubble-content">${isTyping ? '<span class="spinner" style="width:18px;height:18px;border-width:2px;display:inline-block"></span>' : renderMarkdownBasic(escHtml(text))}</div>`;
  messages.appendChild(bubble);
  messages.scrollTop = messages.scrollHeight;
  return id;
}
function removeBubble(id) { document.getElementById(id)?.remove(); }

/* ================================================================
   HELPERS
   ================================================================ */
function setLoading(btn, resultEl, on, isText = false) {
  if (btn) { btn.classList.toggle('loading', on); btn.disabled = on; }
  if (resultEl && on) {
    resultEl.innerHTML = `<div class="ai-loading"><div class="spinner"></div><span>Generating with AI...</span></div>`;
    if (isText) resultEl.style.display = 'flex';
  }
  if (!on && resultEl && isText) resultEl.style.display = '';
}

function escHtml(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function renderMarkdownBasic(text) {
  return text
    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.+?)\*/g, '<em>$1</em>')
    .replace(/^###\s(.+)/gm, '<h4 style="color:#c9a96e;margin:14px 0 6px">$1</h4>')
    .replace(/^##\s(.+)/gm, '<h3 style="color:#c9a96e;margin:16px 0 8px">$1</h3>')
    .replace(/^#\s(.+)/gm, '<h2 style="color:#c9a96e;margin:16px 0 8px">$1</h2>')
    .replace(/^- (.+)/gm, '<li style="margin:4px 0">$1</li>')
    .replace(/\n/g, '<br>');
}

function copyResult(elId) {
  const el = document.getElementById(elId);
  if (!el) return;
  navigator.clipboard.writeText(el.innerText).then(() => showToast('Copied to clipboard!'));
}

function downloadResult(imgId, filename) {
  const img = document.getElementById(imgId);
  if (!img) return;
  const a = document.createElement('a');
  a.href = img.src; a.download = filename; a.click();
}

function copyCode() {
  const pre = document.querySelector('.code-block pre');
  if (!pre) return;
  navigator.clipboard.writeText(pre.innerText).then(() => showToast('Config copied!'));
}

function showToast(msg) {
  const t = document.createElement('div');
  t.textContent = msg;
  Object.assign(t.style, {
    position:'fixed', bottom:'24px', right:'24px', background:'#c9a96e', color:'#fff',
    padding:'10px 20px', borderRadius:'8px', fontSize:'13px', zIndex:'9999',
    boxShadow:'0 4px 20px rgba(0,0,0,.4)', transition:'opacity .3s'
  });
  document.body.appendChild(t);
  setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 2500);
}

// Auto-resize chat textarea
document.getElementById('chatInput')?.addEventListener('input', function() {
  this.style.height = 'auto';
  this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

// Init banner prompt
updateBannerPrompt();
