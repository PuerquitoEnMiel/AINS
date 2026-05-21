// ─── Splash Screen ───────────────────────────────────────────
window.addEventListener('DOMContentLoaded', () => {
    // Only show on first visit per session
    if (!sessionStorage.getItem('splashShown')) {
        sessionStorage.setItem('splashShown', '1');
        setTimeout(() => {
            document.getElementById('splash').classList.add('hidden');
        }, 3400); // 3.4 seconds gives plenty of time for the fusion animation
    } else {
        // Already seen — hide immediately
        const s = document.getElementById('splash');
        if (s) {
            s.style.transition = 'none';
            s.classList.add('hidden');
        }
    }
});

// ─── Sidebar Toggle ──────────────────────────────────────────
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const body = document.body;
    const isCollapsed = sidebar.classList.toggle('collapsed');
    body.classList.toggle('sidebar-collapsed', isCollapsed);
    localStorage.setItem('sidebar-collapsed', isCollapsed ? '1' : '0');
}

// Restore sidebar state on load
(function() {
    if (localStorage.getItem('sidebar-collapsed') === '1') {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
        }
    }
})();

// ─── Mobile Drawer Toggle ────────────────────────────────────
function toggleMobileDrawer() {
    const drawer = document.getElementById('mobile-drawer');
    const panel = document.getElementById('mobile-drawer-panel');
    const backdrop = document.getElementById('mobile-drawer-backdrop');
    
    if (drawer && panel && backdrop) {
        if (drawer.classList.contains('hidden')) {
            drawer.classList.remove('hidden');
            setTimeout(() => {
                panel.classList.remove('-translate-x-full');
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
            }, 10);
        } else {
            panel.classList.add('-translate-x-full');
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            setTimeout(() => {
                drawer.classList.add('hidden');
            }, 300);
        }
    }
}

// ─── Ctrl+K to focus search input ─────────────────────────────
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        const searchInput = document.getElementById('global-search');
        if (searchInput) searchInput.focus();
    }
});

// ─── KBD Badge Fade out & Dynamic AI Tips ────────────────────
const aiTips = [
    "Prompt Tip: Tell AI 'Act as a professional grader...' to get highly analytical writing feedback.",
    "Flipped Learning: Ask the AI 'Test me on [topic] step-by-step and grade my answers.'",
    "Drafting Buddy: Use AI to generate 10 unique outline ideas to break writer's block.",
    "Fact Check: Hallucinations happen! Always double-check facts against trusted academic journals.",
    "Data Safety: Never copy-paste private student names or academic files into public AI systems.",
    "Creative Spark: Ask AI for 'metaphors to explain [complex topic]' to build rich analogies.",
    "Time Saver: Ask AI to 'Summarize this technical article in 3 simple paragraphs for beginners.'"
];

document.addEventListener('DOMContentLoaded', () => {
    // KBD search badge focus transitions
    const globSearch = document.getElementById('global-search');
    const kbdBadge = document.getElementById('search-kbd');
    if (globSearch && kbdBadge) {
        globSearch.addEventListener('focus', () => kbdBadge.classList.add('opacity-0'));
        globSearch.addEventListener('blur', () => {
            if (!globSearch.value) kbdBadge.classList.remove('opacity-0');
        });
    }

    // Dynamic educational tip injector
    const tipElement = document.getElementById('sidebar-ai-tip');
    const mobileTipElement = document.getElementById('mobile-ai-tip');
    if (tipElement || mobileTipElement) {
        const randomTip = aiTips[Math.floor(Math.random() * aiTips.length)];
        if (tipElement) tipElement.textContent = `"${randomTip}"`;
        if (mobileTipElement) mobileTipElement.textContent = `"${randomTip}"`;
    }
});

// ─── AI Companion Chatbot Engine ────────────────────────────────
let chatHistory = [];
let activeConversationId = null;
const csrfToken = 'as4YFYf6u41t6UnJsU6NoFPBxVellUzr4V223hcD';

function toggleChatbot() {
    const panel = document.getElementById('ai-chatbot-panel');
    const fab = document.getElementById('ai-chatbot-fab');
    if (panel) {
        if (panel.classList.contains('opacity-0')) {
            panel.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            panel.classList.add('opacity-100', 'scale-100');
            // Scroll to bottom
            const feed = document.getElementById('chatbot-feed');
            if (feed) feed.scrollTop = feed.scrollHeight;
        } else {
            panel.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            panel.classList.remove('opacity-100', 'scale-100');
        }
    }
}

function sendPresetPrompt(promptText) {
    const input = document.getElementById('chatbot-input');
    if (input) {
        input.value = promptText;
        submitChatQuery();
    }
}

function submitChatQuery() {
    const input = document.getElementById('chatbot-input');
    if (!input) return;
    const query = input.value.trim();
    if (!query) return;

    // Clear input
    input.value = '';

    // Append User Message
    appendChatMessage('user', query);

    // Show Typing Indicator
    showTypingIndicator(true);

    // Call API Securely
    fetch('/api/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            message: query,
            conversation_id: activeConversationId,
            history: chatHistory
        })
    })
    .then(response => response.json())
    .then(data => {
        showTypingIndicator(false);
        const reply = data.reply || "⚠️ *Lo siento, no pude procesar la respuesta.*";
        appendChatMessage('assistant', reply);
        
        if (data.conversation_id) {
            activeConversationId = data.conversation_id;
        }
        
        // Save to History
        chatHistory.push({ role: 'user', content: query });
        chatHistory.push({ role: 'assistant', content: reply });
    })
    .catch(error => {
        showTypingIndicator(false);
        appendChatMessage('assistant', "🔌 *Error de conexión. Por favor verifica tu red e intenta de nuevo.*");
        console.error(error);
    });
}

function appendChatMessage(role, text) {
    const feed = document.getElementById('chatbot-feed');
    if (!feed) return;

    const wrapper = document.createElement('div');
    wrapper.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'} mb-3 items-end gap-2 animate-fade-in-up`;

    // Avatar if assistant
    let avatarHTML = '';
    if (role === 'assistant') {
        avatarHTML = `
            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-ans-dark-green to-ans-light-green flex-shrink-0 flex items-center justify-center text-white text-[10px] font-bold shadow-md shadow-ans-dark-green/10">
                AI
            </div>
        `;
    }

    const bubble = document.createElement('div');
    bubble.className = `max-w-[78%] px-4 py-3 rounded-2xl text-xs leading-relaxed shadow-sm ${
        role === 'user' 
        ? 'bg-gradient-to-r from-ans-dark-green to-ans-seal-green text-white rounded-br-none' 
        : 'bg-white/90 text-gray-800 border border-gray-100 rounded-bl-none backdrop-blur-sm'
    }`;

    // Simple markdown formatting
    bubble.innerHTML = parseBotMarkdown(text);
    
    if (role === 'assistant') {
        wrapper.appendChild(bubble);
        feed.appendChild(wrapper);
    } else {
        wrapper.appendChild(bubble);
        feed.appendChild(wrapper);
    }

    // Scroll to bottom
    feed.scrollTop = feed.scrollHeight;
}

function showTypingIndicator(show) {
    let indicator = document.getElementById('chatbot-typing-indicator');
    const feed = document.getElementById('chatbot-feed');
    if (!feed) return;

    if (show) {
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'chatbot-typing-indicator';
            indicator.className = 'flex justify-start mb-3 items-center gap-2 animate-pulse';
            indicator.innerHTML = `
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-ans-dark-green to-ans-light-green flex-shrink-0 flex items-center justify-center text-white text-[10px] font-bold shadow-md">
                    AI
                </div>
                <div class="bg-white/80 border border-gray-100 px-4 py-2.5 rounded-2xl rounded-bl-none text-xs text-gray-400 flex items-center gap-1.5 backdrop-blur-sm">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                </div>
            `;
            feed.appendChild(indicator);
        }
        feed.scrollTop = feed.scrollHeight;
    } else {
        if (indicator) {
            indicator.remove();
        }
    }
}

function parseBotMarkdown(text) {
    // Bold
    let formatted = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
    // Code blocks
    formatted = formatted.replace(/```([\s\S]*?)```/g, '<pre class="bg-gray-900/90 text-white rounded-xl p-3 my-2 font-mono text-[10px] overflow-x-auto whitespace-pre">$1</pre>');
    // Bullet lists
    formatted = formatted.replace(/^\s*-\s+(.*?)$/gm, '<li class="ml-4 list-disc">$1</li>');
    // Paragraphs / Newlines
    formatted = formatted.replace(/\n/g, '<br>');
    return formatted;
}

// Bind Enter Key
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('chatbot-input');
    if (input) {
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitChatQuery();
            }
        });
    }
});