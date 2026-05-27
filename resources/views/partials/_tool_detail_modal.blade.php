{{-- Tool Detail Modal Component --}}
<div id="tool-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 md:p-6" onclick="if(event.target===this)closeToolModal()">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md opacity-0 transition-opacity duration-300" id="modal-backdrop"></div>
    <!-- Panel -->
    <div class="relative w-full max-w-2xl bg-white/95 backdrop-blur-xl border border-gray-100 rounded-3xl shadow-2xl flex flex-col transform transition-all duration-300 scale-95 opacity-0 overflow-hidden max-h-[90vh]" id="modal-panel">
        <!-- Header -->
        <div class="relative bg-gradient-to-br from-ans-dark-green to-ans-seal-green p-6 flex-shrink-0">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
            <div class="absolute top-4 right-4 flex items-center gap-2">
                <button onclick="toggleFavCurrent()" id="modal-fav-btn" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/80 hover:text-white transition-all shadow-sm" title="Favorite">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.381-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                </button>
                <button onclick="closeToolModal()" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/80 hover:text-white transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex items-center gap-4 pr-20">
                <div id="modal-logo" class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center border-2 border-white/20 flex-shrink-0 overflow-hidden shadow-md">
                    <span id="modal-logo-letter" class="text-white font-bold text-2xl"></span>
                </div>
                <div class="min-w-0">
                    <p class="text-white/60 text-xs font-bold uppercase tracking-wider mb-0.5" id="modal-category"></p>
                    <h2 class="text-2xl font-heading font-black text-white truncate leading-tight" id="modal-name"></h2>
                    <span id="modal-type-badge" class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider mt-1.5 inline-block"></span>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs Bar -->
        <div class="flex border-b border-gray-100 bg-gray-50/50 px-6 flex-shrink-0">
            <button onclick="switchModalTab('overview')" id="tab-btn-overview" class="py-3.5 px-4 text-sm font-semibold border-b-2 border-ans-dark-green text-ans-dark-green transition-all hover:text-ans-dark-green flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Overview
            </button>
            <button onclick="switchModalTab('reviews')" id="tab-btn-reviews" class="py-3.5 px-4 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-900 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                Reviews &amp; Ratings
                <span id="reviews-badge-count" class="hidden text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">0</span>
            </button>
        </div>

        <!-- Body Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
            <!-- Tab 1: Overview -->
            <div id="modal-tab-overview" class="space-y-6">
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">About this tool</h3>
                    <p class="text-gray-750 leading-relaxed text-sm md:text-base" id="modal-desc"></p>
                </div>

                <!-- Device Compatibility Section -->
                <div class="border-t border-gray-100 pt-5">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Device Compatibility</h3>
                    <div id="modal-compatibility-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <!-- Will be populated dynamically by JavaScript -->
                    </div>
                </div>

                <!-- Usage metrics & Link Card -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-100 pt-5">
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex flex-col justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Official URL</span>
                        <a id="modal-url" href="#" target="_blank" class="text-ans-dark-green text-sm font-bold hover:underline break-all block mt-1"></a>
                    </div>
                    <div class="p-4 bg-ans-dark-green/5 rounded-2xl border border-ans-dark-green/10 flex flex-col justify-between">
                        <span class="text-xs font-bold text-ans-dark-green/60 uppercase tracking-wider mb-1">Usage Count</span>
                        <div id="modal-clicks-badge" class="flex items-center gap-1.5 text-ans-dark-green font-bold text-sm mt-1">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            <span>Used <span id="modal-clicks-count">0</span> times in community</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Reviews & Ratings -->
            <div id="modal-tab-reviews" class="hidden space-y-6">
                <!-- Summary Card -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 bg-gray-50/80 rounded-2xl border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="text-center sm:text-left">
                            <div class="flex items-baseline justify-center sm:justify-start gap-1">
                                <span id="modal-avg-rating" class="text-3xl font-black text-gray-900">0.0</span>
                                <span class="text-sm font-semibold text-gray-400">/ 5.0</span>
                            </div>
                            <div class="flex items-center justify-center sm:justify-start gap-0.5 mt-0.5 text-yellow-400" id="modal-stars-container">
                                <!-- Dynamic Star SVG elements -->
                            </div>
                            <p class="text-xs text-gray-500 mt-1" id="modal-total-reviews">0 reviews</p>
                        </div>
                    </div>

                    @auth
                    <button onclick="toggleReviewForm()" id="write-review-btn" class="w-full sm:w-auto bg-ans-dark-green hover:bg-ans-seal-green text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-md flex items-center justify-center gap-1.5 hover:-translate-y-0.5 active:translate-y-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span id="review-btn-text">Write a Review</span>
                    </button>
                    @else
                    <p class="text-xs text-gray-500 italic bg-gray-200/50 px-3 py-2 rounded-lg">Log in to post a review</p>
                    @endauth
                </div>

                <!-- Stars distribution details -->
                <div id="modal-rating-distribution" class="space-y-2 text-xs">
                    <!-- Dynamic distribution bars -->
                </div>

                @auth
                <!-- Add/Edit Review Inline Form -->
                <div id="review-form-container" class="hidden p-5 bg-white border border-ans-dark-green/20 rounded-2xl shadow-sm space-y-4 animate-fade-in-up">
                    <h4 class="text-sm font-bold text-gray-900" id="review-form-title">Your Review</h4>
                    <form id="ajax-review-form" onsubmit="submitReviewAJAX(event)">
                        @csrf
                        <div class="space-y-3.5">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Rating</label>
                                <div class="flex items-center gap-1.5">
                                    <input type="hidden" name="rating" id="review-rating-value" value="5">
                                    <div class="flex gap-1" id="rating-star-selector">
                                        @for($i = 1; $i <= 5; $i++)
                                        <button type="button" onclick="setFormRating({{ $i }})" class="star-select-btn text-yellow-400 transition-transform hover:scale-110 focus:outline-none" data-value="{{ $i }}">
                                            <svg class="w-8 h-8 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        </button>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="review-comment" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Comment (Optional)</label>
                                <textarea name="comment" id="review-comment" rows="3" class="block w-full rounded-xl border border-gray-200 p-3 text-sm focus:border-ans-dark-green focus:ring-4 focus:ring-ans-dark-green/10 focus:outline-none placeholder-gray-400" placeholder="Describe your experience with this tool..."></textarea>
                            </div>
                            <div class="flex items-center justify-end gap-2.5">
                                <button type="button" onclick="toggleReviewForm()" class="px-4 py-2 border border-gray-200 text-gray-500 rounded-xl text-xs font-bold hover:bg-gray-50 transition-all">Cancel</button>
                                <button type="submit" id="review-submit-btn" class="px-5 py-2.5 bg-ans-dark-green text-white rounded-xl text-xs font-bold hover:bg-ans-seal-green transition-all shadow-md flex items-center gap-1.5">
                                    <span id="review-submit-text">Submit</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @endauth

                <!-- Reviews Feed -->
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">User Reviews</h4>
                    <div id="modal-reviews-feed" class="space-y-4">
                        <!-- Dynamic Reviews List -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-6 border-t border-gray-100 bg-gray-50/50 flex-shrink-0 flex gap-4">
            <a id="modal-open-btn" href="#" target="_blank"
               class="flex-1 flex items-center justify-center gap-2 bg-gradient-to-r from-ans-dark-green to-ans-seal-green hover:from-ans-seal-green hover:to-ans-dark-green text-white font-bold py-4 rounded-2xl shadow-lg shadow-ans-dark-green/20 hover:shadow-xl hover:-translate-y-0.5 transition-all text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                Open Tool
            </a>
        </div>
    </div>
</div>

<script>
// State & Core Logic for Tool Detail Modal & Favorites
let activeTool = null;
const serverFavs = {!! json_encode(Auth::check() ? Auth::user()->favorites()->pluck('name')->toArray() : []) !!};
let userReviewData = null;

// Favorites Helpers
function getFavs() {
    let favs = [];
    try {
        favs = JSON.parse(localStorage.getItem('ains-favs')) || [];
    } catch(e) {}
    
    if ({!! json_encode(Auth::check()) !!}) {
        return [...new Set([...serverFavs, ...favs])];
    }
    return favs;
}

function toggleFav(name) {
    let favs = getFavs();
    if (favs.includes(name)) {
        favs = favs.filter(n => n !== name);
    } else {
        favs.push(name);
    }
    localStorage.setItem('ains-favs', JSON.stringify(favs));
    return favs.includes(name);
}

function updateCardHeartUI(btn, isFav) {
    const svg = btn.querySelector('svg');
    if (!svg) return;
    if (isFav) {
        btn.classList.remove('text-gray-400');
        btn.classList.add('text-red-500');
        svg.setAttribute('fill', 'currentColor');
    } else {
        btn.classList.remove('text-red-500');
        btn.classList.add('text-gray-400');
        svg.setAttribute('fill', 'none');
    }
}

function syncHeartsForTool(toolName, isFav) {
    document.querySelectorAll(`.card-fav-btn[data-tool-name="${toolName}"]`).forEach(btn => {
        updateCardHeartUI(btn, isFav);
    });
}

function toggleCardFavorite(event, btn, toolName, toolId) {
    if (event) event.stopPropagation();
    const isFav = toggleFav(toolName);
    syncHeartsForTool(toolName, isFav);
    if (typeof applyFilters === 'function') applyFilters();
    
    const csrfTokenEl = document.querySelector('meta[name="csrf-token"]');
    if (csrfTokenEl && toolId) {
        const csrfToken = csrfTokenEl.getAttribute('content');
        fetch(`/tools/${toolId}/favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 401 || response.status === 403) {
                    toggleFav(toolName);
                    syncHeartsForTool(toolName, !isFav);
                    if (typeof applyFilters === 'function') applyFilters();
                }
                return response.json().then(data => { throw new Error(data.error || 'Sync error') });
            }
            return response.json();
        })
        .then(data => {
            if (data.favorited !== isFav) {
                localStorage.setItem('ains-favs', JSON.stringify(
                    data.favorited 
                        ? [...new Set([...getFavs(), toolName])]
                        : getFavs().filter(n => n !== toolName)
                ));
                syncHeartsForTool(toolName, data.favorited);
                if (typeof applyFilters === 'function') applyFilters();
            }
        })
        .catch(err => console.error('Error syncing favorite:', err));
    }
}

function toggleFavCurrent() {
    if (!activeTool) return;
    const isFav = toggleFav(activeTool.name);
    updateFavBtnState(isFav);
    syncHeartsForTool(activeTool.name, isFav);
    if (typeof applyFilters === 'function') applyFilters();
    
    const csrfTokenEl = document.querySelector('meta[name="csrf-token"]');
    if (csrfTokenEl && activeTool.id) {
        const csrfToken = csrfTokenEl.getAttribute('content');
        fetch(`/tools/${activeTool.id}/favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).catch(err => console.error('Error syncing favorite:', err));
    }
}

function initHearts() {
    const favs = getFavs();
    document.querySelectorAll('.card-fav-btn').forEach(btn => {
        const name = btn.dataset.toolName;
        updateCardHeartUI(btn, favs.includes(name));
    });
}

function updateFavBtnState(isFav) {
    const btn = document.getElementById('modal-fav-btn');
    if (!btn) return;
    if (isFav) {
        btn.innerHTML = `<svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>`;
        btn.title = "Remove from Favorites";
    } else {
        btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.381-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>`;
        btn.title = "Add to Favorites";
    }
}

// Click Metrics helpers
function getClicks(name) {
    try {
        const clicks = JSON.parse(localStorage.getItem('ains-clicks')) || {};
        return clicks[name] || 0;
    } catch(e) {
        return 0;
    }
}

function incrementClicks(name) {
    try {
        const clicks = JSON.parse(localStorage.getItem('ains-clicks')) || {};
        clicks[name] = (clicks[name] || 0) + 1;
        localStorage.setItem('ains-clicks', JSON.stringify(clicks));
        return clicks[name];
    } catch(e) {
        return 1;
    }
}

function openToolModal(tool) {
    activeTool = tool;
    
    switchModalTab('overview');
    
    const reviewForm = document.getElementById('review-form-container');
    if (reviewForm) reviewForm.classList.add('hidden');
    const writeBtnText = document.getElementById('review-btn-text');
    if (writeBtnText) writeBtnText.textContent = "Write a Review";

    document.getElementById('modal-name').textContent = tool.name;
    document.getElementById('modal-category').textContent = tool.cat || 'General';
    document.getElementById('modal-desc').textContent = tool.desc;
    document.getElementById('modal-url').textContent = tool.url;
    document.getElementById('modal-url').href = tool.url;
    document.getElementById('modal-open-btn').href = tool.url;

    const logoEl = document.getElementById('modal-logo');
    logoEl.className = "w-16 h-16 rounded-2xl flex items-center justify-center border-2 border-white/20 flex-shrink-0 overflow-hidden shadow-md";
    if (tool.logo) {
        logoEl.classList.add("bg-white/10");
        logoEl.innerHTML = `<img src="${tool.logo}" class="w-full h-full object-cover">`;
    } else {
        const vibrantGradients = [
            'from-purple-500 to-indigo-600',
            'from-pink-500 to-rose-650',
            'from-emerald-500 to-teal-600',
            'from-blue-500 to-indigo-600',
            'from-amber-500 to-orange-600',
            'from-cyan-500 to-blue-600',
            'from-violet-500 to-fuchsia-600',
            'from-red-500 to-ans-orange'
        ];
        let hash = 0;
        for (let i = 0; i < tool.name.length; i++) {
            hash += tool.name.charCodeAt(i);
        }
        const grad = vibrantGradients[hash % vibrantGradients.length];
        logoEl.className = "w-16 h-16 rounded-2xl flex items-center justify-center border-2 border-white/20 flex-shrink-0 overflow-hidden shadow-md bg-gradient-to-br " + grad;
        logoEl.innerHTML = `<span class="text-white font-bold text-2xl shadow-sm">${tool.name.charAt(0)}</span>`;
    }

    const badge = document.getElementById('modal-type-badge');
    if (tool.type === 'Google Workspace' || tool.type === 'Workspace') {
        badge.textContent = 'Google Workspace';
        badge.className = 'text-[10px] font-bold bg-white/20 text-white px-2.5 py-1 rounded-full uppercase tracking-wider mt-2 inline-block';
    } else {
        badge.textContent = tool.type || '3rd Party';
        badge.className = 'text-[10px] font-bold bg-ans-orange/20 text-ans-orange px-2.5 py-1 rounded-full uppercase tracking-wider mt-2 inline-block';
    }

    updateFavBtnState(getFavs().includes(tool.name));

    const clickCount = getClicks(tool.name);
    document.getElementById('modal-clicks-count').textContent = clickCount;

    renderDeviceCompatibility(tool.compatibility);

    document.getElementById('modal-avg-rating').textContent = parseFloat(tool.avg_rating || 0).toFixed(1);
    updateStarsContainer('modal-stars-container', tool.avg_rating || 0);

    const modal = document.getElementById('tool-modal');
    const panel = document.getElementById('modal-panel');
    const backdrop = document.getElementById('modal-backdrop');
    
    modal.classList.remove('hidden');
    panel.offsetHeight;
    backdrop.classList.add('opacity-100');
    panel.classList.remove('scale-95', 'opacity-0');
    panel.classList.add('scale-100', 'opacity-100');
    document.body.style.overflow = 'hidden';

    if (tool.id) {
        fetchReviewsData(tool.id);
    } else {
        renderDemoReviews(tool.name);
    }
}

function closeToolModal() {
    const modal = document.getElementById('tool-modal');
    const panel = document.getElementById('modal-panel');
    const backdrop = document.getElementById('modal-backdrop');
    
    if (panel) {
        panel.classList.add('scale-95', 'opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
    }
    if (backdrop) {
        backdrop.classList.remove('opacity-100');
    }
    
    setTimeout(() => {
        if (modal) modal.classList.add('hidden');
        document.body.style.overflow = '';
        activeTool = null;
    }, 300);
}

document.addEventListener('keydown', e => { if(e.key === 'Escape') closeToolModal(); });

// Bind clicks on open button to track institutional usage
const openBtn = document.getElementById('modal-open-btn');
if (openBtn) {
    openBtn.addEventListener('click', () => {
        if (activeTool) {
            const newCount = incrementClicks(activeTool.name);
            document.getElementById('modal-clicks-count').textContent = newCount;
        }
    });
}

function renderDeviceCompatibility(compatibilityStr) {
    const grid = document.getElementById('modal-compatibility-grid');
    if (!grid) return;
    grid.innerHTML = '';
    
    const devices = [
        { name: 'Chromebook', icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>` },
        { name: 'iPad', icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>` },
        { name: 'Windows', icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v8.026H3V4.062a.96.96 0 01.696-.924L9.75 3.104zm1.5 8.026V3.06l6.054-1.009a.96.96 0 011.196.924V11.13h-7.25zM3 12.87h6.75V20.9l-6.054-.959A.96.96 0 013 19.017V12.87zm8.25 0H21v6.182a.96.96 0 01-1.196.924l-6.054-1.009V12.87z"></path></svg>` },
        { name: 'MacOS', icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C3.8 16.32 4.67 9 9.3 8.76c1.2.06 2 .69 2.68.68.73-.02 1.73-.78 3.14-.64 1.74.17 3.06.94 3.73 2.11-3.6 2.16-3.03 6.86.6 8.35-.74 1.88-1.57 3.74-3.32 5.02zM12 8.2c-.07-2.93 2.4-5.36 5.3-5.4.3 3.42-2.9 5.86-5.3 5.4z"></path></svg>` },
        { name: 'iOS', icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>` },
        { name: 'Android', icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 21v-4m6 4v-4M4 11h16M4 8.5h16A1.5 1.5 0 0121.5 10v7A1.5 1.5 0 0120 18.5H4A1.5 1.5 0 014 8.5z"></path></svg>` }
    ];
    
    let allowedDevices = [];
    if (compatibilityStr) {
        allowedDevices = compatibilityStr.split(',').map(d => d.trim().toLowerCase());
    } else {
        allowedDevices = devices.map(d => d.name.toLowerCase());
    }
    
    devices.forEach(device => {
        const isAllowed = allowedDevices.includes(device.name.toLowerCase());
        const pill = document.createElement('div');
        
        if (isAllowed) {
            pill.className = "flex items-center gap-2 px-3 py-2.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl text-xs font-semibold shadow-sm";
            pill.innerHTML = `
                <span class="flex-shrink-0 text-emerald-500">${device.icon}</span>
                <span class="truncate">${device.name}</span>
                <svg class="w-3.5 h-3.5 ml-auto text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            `;
        } else {
            pill.className = "flex items-center gap-2 px-3 py-2.5 bg-gray-50/50 text-gray-400 border border-gray-100 rounded-xl text-xs font-medium opacity-60";
            pill.innerHTML = `
                <span class="flex-shrink-0 text-gray-400/80">${device.icon}</span>
                <span class="truncate">${device.name}</span>
                <svg class="w-3.5 h-3.5 ml-auto text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            `;
        }
        grid.appendChild(pill);
    });
}

function switchModalTab(tab) {
    const btnOverview = document.getElementById('tab-btn-overview');
    const btnReviews = document.getElementById('tab-btn-reviews');
    const contentOverview = document.getElementById('modal-tab-overview');
    const contentReviews = document.getElementById('modal-tab-reviews');
    
    if (!btnOverview || !btnReviews) return;
    
    if (tab === 'overview') {
        btnOverview.className = "py-3.5 px-4 text-sm font-semibold border-b-2 border-ans-dark-green text-ans-dark-green transition-all flex items-center gap-2";
        btnReviews.className = "py-3.5 px-4 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-900 transition-all flex items-center gap-2";
        if (contentOverview) contentOverview.classList.remove('hidden');
        if (contentReviews) contentReviews.classList.add('hidden');
    } else {
        btnOverview.className = "py-3.5 px-4 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-900 transition-all flex items-center gap-2";
        btnReviews.className = "py-3.5 px-4 text-sm font-semibold border-b-2 border-ans-dark-green text-ans-dark-green transition-all flex items-center gap-2";
        if (contentOverview) contentOverview.classList.add('hidden');
        if (contentReviews) contentReviews.classList.remove('hidden');
    }
}

function updateStarsContainer(containerId, rating) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = '';
    
    const fullRating = parseFloat(rating || 0);
    const floorRating = Math.floor(fullRating);
    const hasHalf = (fullRating - floorRating) >= 0.4;
    
    for (let i = 1; i <= 5; i++) {
        let starClass = 'text-gray-200 fill-current';
        if (i <= floorRating) {
            starClass = 'text-yellow-400 fill-current';
        } else if (i === floorRating + 1 && hasHalf) {
            starClass = 'text-yellow-400/50 fill-current';
        }
        
        container.innerHTML += `
            <svg class="w-4 h-4 ${starClass}" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
        `;
    }
}

function renderRatingDistribution(reviews) {
    const container = document.getElementById('modal-rating-distribution');
    if (!container) return;
    container.innerHTML = '';
    
    if (!reviews || reviews.length === 0) return;
    
    const totals = { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
    reviews.forEach(r => {
        if (totals[r.rating] !== undefined) totals[r.rating]++;
    });
    
    const count = reviews.length;
    
    for (let stars = 5; stars >= 1; stars--) {
        const percent = Math.round((totals[stars] / count) * 100);
        const row = document.createElement('div');
        row.className = "flex items-center gap-3";
        row.innerHTML = `
            <span class="w-12 text-right font-medium text-gray-500">${stars} estrellas</span>
            <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-yellow-400 rounded-full transition-all duration-500" style="width: ${percent}%"></div>
            </div>
            <span class="w-8 text-left text-gray-400 font-semibold">${percent}%</span>
        `;
        container.appendChild(row);
    }
}

function renderReviewsList(reviews) {
    const feed = document.getElementById('modal-reviews-feed');
    if (!feed) return;
    feed.innerHTML = '';
    
    if (!reviews || reviews.length === 0) {
        feed.innerHTML = `
            <div class="text-center py-10 border-2 border-dashed border-gray-100 rounded-2xl p-6">
                <p class="text-sm font-semibold text-gray-400">No reviews yet</p>
                <p class="text-xs text-gray-400 mt-1">Be the first to share your experience with this tool!</p>
            </div>
        `;
        return;
    }
    
    reviews.forEach(review => {
        const card = document.createElement('div');
        card.className = "p-4 bg-gray-50/70 border border-gray-100 rounded-2xl flex gap-3.5 items-start shadow-sm";
        
        const userInit = review.user && review.user.name ? review.user.name.charAt(0).toUpperCase() : 'U';
        let avatarHTML = '';
        if (review.user && review.user.avatar) {
            avatarHTML = `<img src="${review.user.avatar}" class="w-10 h-10 rounded-full object-cover shadow-sm">`;
        } else {
            const colors = ['bg-indigo-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500', 'bg-teal-500', 'bg-sky-500'];
            let h = 0;
            if (review.user && review.user.name) {
                for (let i = 0; i < review.user.name.length; i++) h += review.user.name.charCodeAt(i);
            }
            const color = colors[h % colors.length];
            avatarHTML = `<div class="w-10 h-10 rounded-full ${color} text-white font-bold flex items-center justify-center shadow-sm text-sm">${userInit}</div>`;
        }

        let starsHTML = '';
        for (let i = 1; i <= 5; i++) {
            const active = i <= review.rating ? 'text-yellow-400 fill-current' : 'text-gray-200 fill-current';
            starsHTML += `<svg class="w-3.5 h-3.5 ${active}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>`;
        }

        let roleBadge = '';
        const role = review.user && review.user.role ? review.user.role.toLowerCase() : 'student';
        if (role === 'admin') {
            roleBadge = `<span class="text-[9px] font-extrabold bg-red-500/10 text-red-600 px-1.5 py-0.5 rounded-md uppercase tracking-wider">Admin</span>`;
        } else if (role === 'teacher') {
            roleBadge = `<span class="text-[9px] font-extrabold bg-emerald-500/10 text-emerald-600 px-1.5 py-0.5 rounded-md uppercase tracking-wider">Teacher</span>`;
        } else {
            roleBadge = `<span class="text-[9px] font-extrabold bg-purple-500/10 text-purple-600 px-1.5 py-0.5 rounded-md uppercase tracking-wider">Student</span>`;
        }

        const reviewDate = new Date(review.updated_at || review.created_at);
        const formattedDate = reviewDate.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });

        card.innerHTML = `
            <div class="flex-shrink-0">${avatarHTML}</div>
            <div class="min-w-0 flex-1 space-y-1">
                <div class="flex flex-wrap items-center justify-between gap-1">
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-gray-900 text-sm truncate">${review.user ? review.user.name : 'Unknown User'}</span>
                        ${roleBadge}
                    </div>
                    <span class="text-[10px] text-gray-400 font-semibold">${formattedDate}</span>
                </div>
                <div class="flex items-center gap-0.5">${starsHTML}</div>
                <p class="text-xs text-gray-650 leading-relaxed mt-1.5 break-words">${review.comment || '<span class="italic text-gray-400">No comment left.</span>'}</p>
            </div>
        `;
        feed.appendChild(card);
    });
}

function renderDemoReviews(toolName) {
    const badgeCount = document.getElementById('reviews-badge-count');
    if (badgeCount) badgeCount.classList.add('hidden');
    
    document.getElementById('modal-total-reviews').textContent = 'Mock Reviews (Demo)';
    document.getElementById('modal-avg-rating').textContent = '4.7';
    updateStarsContainer('modal-stars-container', 4.7);
    
    const feed = document.getElementById('modal-reviews-feed');
    if (feed) {
        feed.innerHTML = `
            <div class="text-center py-6">
                <p class="text-xs text-gray-400 italic">Reviews not available for demo tools.</p>
            </div>
        `;
    }
}

function fetchReviewsData(toolId) {
    const feed = document.getElementById('modal-reviews-feed');
    if (!feed) return;
    
    feed.innerHTML = `
        <div class="flex flex-col items-center justify-center py-8 text-gray-400 gap-2">
            <svg class="w-8 h-8 animate-spin text-ans-dark-green" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-xs font-semibold">Loading reviews...</span>
        </div>
    `;
    
    fetch(`/tools/${toolId}?ajax=1`)
        .then(res => res.json())
        .then(data => {
            if (activeTool && activeTool.id === toolId) {
                const avg = parseFloat(data.tool.avg_rating || 0);
                document.getElementById('modal-avg-rating').textContent = avg.toFixed(1);
                updateStarsContainer('modal-stars-container', avg);
                
                const reviewsCount = data.reviews ? data.reviews.length : 0;
                document.getElementById('modal-total-reviews').textContent = `${reviewsCount} ${reviewsCount === 1 ? 'review' : 'reviews'}`;
                
                const badgeCount = document.getElementById('reviews-badge-count');
                if (badgeCount) {
                    badgeCount.textContent = reviewsCount;
                    badgeCount.classList.remove('hidden');
                }
                
                userReviewData = data.userReview;
                const formTitle = document.getElementById('review-form-title');
                const submitText = document.getElementById('review-submit-text');
                const writeBtnText = document.getElementById('review-btn-text');
                
                if (userReviewData) {
                    if (writeBtnText) writeBtnText.textContent = "Edit Review";
                    if (formTitle) formTitle.textContent = "Update Your Review";
                    if (submitText) submitText.textContent = "Update Review";
                    setFormRating(userReviewData.rating);
                    const commentArea = document.getElementById('review-comment');
                    if (commentArea) commentArea.value = userReviewData.comment || '';
                } else {
                    if (writeBtnText) writeBtnText.textContent = "Write a Review";
                    if (formTitle) formTitle.textContent = "Write a Review";
                    if (submitText) submitText.textContent = "Post Review";
                    setFormRating(5);
                    const commentArea = document.getElementById('review-comment');
                    if (commentArea) commentArea.value = '';
                }

                renderRatingDistribution(data.reviews);
                renderReviewsList(data.reviews);
            }
        })
        .catch(err => {
            console.error('Error fetching reviews:', err);
            feed.innerHTML = '<p class="text-xs text-red-500 text-center py-4">Failed to load reviews.</p>';
        });
}

function setFormRating(rating) {
    const input = document.getElementById('review-rating-value');
    if (input) input.value = rating;
    
    const buttons = document.querySelectorAll('.star-select-btn');
    buttons.forEach((btn, idx) => {
        if (idx < rating) {
            btn.className = "star-select-btn text-yellow-400 transition-transform scale-110 focus:outline-none";
        } else {
            btn.className = "star-select-btn text-gray-200 hover:text-yellow-300 transition-transform focus:outline-none";
        }
    });
}

function toggleReviewForm() {
    const form = document.getElementById('review-form-container');
    const writeBtnText = document.getElementById('review-btn-text');
    if (!form) return;
    
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
        if (writeBtnText) writeBtnText.textContent = "Hide Form";
    } else {
        form.classList.add('hidden');
        if (writeBtnText) {
            writeBtnText.textContent = userReviewData ? "Edit Review" : "Write a Review";
        }
    }
}

function submitReviewAJAX(event) {
    event.preventDefault();
    if (!activeTool || !activeTool.id) return;
    
    const submitBtn = document.getElementById('review-submit-btn');
    const submitText = document.getElementById('review-submit-text');
    if (!submitBtn || !submitText) return;
    
    const originalText = submitText.textContent;
    submitBtn.disabled = true;
    submitText.innerHTML = `
        <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Saving...
    `;
    
    const form = document.getElementById('ajax-review-form');
    const formData = new FormData(form);
    
    fetch(`/tools/${activeTool.id}/reviews`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(async res => {
        if (!res.ok) {
            const data = await res.json();
            throw new Error(data.message || 'Validation failed');
        }
        return res.json();
    })
    .then(data => {
        toggleReviewForm();
        fetchReviewsData(activeTool.id);
    })
    .catch(err => {
        alert(err.message || 'Error saving review. Please try again.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitText.textContent = originalText;
    });
}

// Initial hearts sync on load
document.addEventListener('DOMContentLoaded', () => {
    if (typeof initHearts === 'function') initHearts();
});
</script>
