@extends('layouts.app')

@section('title', 'Learning Hub — STEM & Innovation Resources')

@section('content')
{{-- ─────────────────────────────────────────────────────────────────────── --}}
{{-- HERO BANNER                                                              --}}
{{-- ─────────────────────────────────────────────────────────────────────── --}}
<div class="resource-hero">
    <div class="resource-hero__bg"></div>
    <div class="resource-hero__particles" id="resourceParticles"></div>
    <div class="resource-hero__content">
        <div class="resource-hero__badge">
            <span>🚀</span> STEM &amp; Innovation Hub
        </div>
        <h1 class="resource-hero__title">
            Learn. Innovate. <span class="resource-hero__title--accent">Transform.</span>
        </h1>
        <p class="resource-hero__subtitle">
            Curated books, courses, tools, and links for the innovators of tomorrow.
        </p>

        {{-- Role-specific sub-tagline --}}
        @auth
            @if(auth()->user()->isStudent())
                <p class="resource-hero__role-tag">🎓 Resources selected for <strong>Students</strong></p>
            @elseif(auth()->user()->isTeacher())
                <p class="resource-hero__role-tag">👩‍🏫 Resources for <strong>Educators</strong></p>
            @elseif(auth()->user()->isAdmin())
                <p class="resource-hero__role-tag">🛡️ Admin view — full access</p>
            @endif
        @endauth

        {{-- Stats row --}}
        <div class="resource-hero__stats">
            <div class="resource-hero__stat">
                <span class="resource-hero__stat-num animate-stat-count" data-target="{{ \App\Models\Resource::published()->count() }}">{{ \App\Models\Resource::published()->count() }}</span>
                <span class="resource-hero__stat-label">Resources</span>
            </div>
            <div class="resource-hero__stat">
                <span class="resource-hero__stat-num animate-stat-count" data-target="9">9</span>
                <span class="resource-hero__stat-label">Areas</span>
            </div>
            <div class="resource-hero__stat">
                <span class="resource-hero__stat-num animate-stat-count" data-target="6">6</span>
                <span class="resource-hero__stat-label">Formats</span>
            </div>
        </div>
    </div>
</div>

{{-- ─────────────────────────────────────────────────────────────────────── --}}
{{-- MAIN CONTENT                                                             --}}
{{-- ─────────────────────────────────────────────────────────────────────── --}}
<div class="resource-page" x-data="resourceHub()" x-init="init()">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flash-success" x-data="{show:true}" x-show="show" x-transition>
            <span>✅ {{ session('success') }}</span>
            <button @click="show=false">✕</button>
        </div>
    @endif

    {{-- ─── TABS ──────────────────────────────────────────────────────── --}}
    <div class="resource-tabs">
        <a href="{{ route('resources.index', array_merge(request()->except('tab'), ['tab'=>'all'])) }}"
           class="resource-tab {{ $tab === 'all' ? 'resource-tab--active' : '' }}">
            🌐 All Resources
        </a>

        @auth
        <a href="{{ route('resources.index', array_merge(request()->except('tab'), ['tab'=>'saved'])) }}"
           class="resource-tab {{ $tab === 'saved' ? 'resource-tab--active' : '' }}">
            ♡ Saved
            @if(count($savedIds) > 0)
                <span class="resource-tab__badge">{{ count($savedIds) }}</span>
            @endif
        </a>

        @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
        <a href="{{ route('resources.index', array_merge(request()->except('tab'), ['tab'=>'proposals'])) }}"
           class="resource-tab {{ $tab === 'proposals' ? 'resource-tab--active' : '' }}">
            📝 My Proposals
        </a>
        @endif

        @if(auth()->user()->isAdmin() && $pendingCount > 0)
        <a href="{{ route('admin.resources.index', ['status'=>'pending']) }}"
           class="resource-tab resource-tab--pending">
            ⏳ Pending Approval
            <span class="resource-tab__badge resource-tab__badge--warn">{{ $pendingCount }}</span>
        </a>
        @endif
        @endauth
    </div>

    {{-- ─── FILTERS ─────────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('resources.index') }}" class="resource-filters" id="resource-filter-form">
        <input type="hidden" name="tab" value="{{ $tab }}">

        {{-- Search --}}
        <div class="resource-search-wrap">
            <span class="resource-search-icon">🔍</span>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search resources, authors, platforms..."
                class="resource-search-input"
                @input="debounceSubmit()"
            >
        </div>

        {{-- Area chips --}}
        <div class="resource-filter-chips">
            @php
                $areas = ['all'=>'All Areas', 'stem'=>'STEM', 'innovation'=>'Innovation', 'ai'=>'AI', 'robotics'=>'Robotics', 'design'=>'Design', 'programming'=>'Programming', 'math'=>'Math', 'science'=>'Science', 'general'=>'General'];
            @endphp
            @foreach($areas as $value => $label)
                <button type="submit" name="area" value="{{ $value }}"
                    class="resource-chip {{ $area === $value ? 'resource-chip--active' : '' }} {{ $value !== 'all' ? 'resource-chip--area resource-chip--area-' . $value : '' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Type chips --}}
        <div class="resource-filter-chips">
            @php
                $types = ['all'=>'📄 All Types', 'link'=>'🔗 Link', 'book'=>'📖 Book', 'video'=>'🎥 Video', 'article'=>'📰 Article', 'tool'=>'🛠️ Tool', 'course'=>'🎓 Course'];
            @endphp
            @foreach($types as $value => $label)
                <button type="submit" name="type" value="{{ $value }}"
                    class="resource-chip {{ $type === $value ? 'resource-chip--active' : '' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </form>

    {{-- ─── TEACHER/ADMIN ACTION BAR ───────────────────────────────── --}}
    @auth
    @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
    <div class="resource-action-bar">
        @if(auth()->user()->isTeacher())
            <a href="{{ route('resources.propose') }}" class="resource-btn-propose">
                <span>+</span> Propose a Resource
            </a>
        @endif
        @if(auth()->user()->isAdmin())
            <a href="{{ route('resources.propose') }}" class="resource-btn-propose">
                <span>+</span> Add Resource
            </a>
            <a href="{{ route('admin.resources.index') }}" class="resource-btn-admin">
                🛡️ Manage All Resources
                @if($pendingCount > 0)
                    <span class="resource-btn-admin__badge">{{ $pendingCount }} pending</span>
                @endif
            </a>
        @endif
    </div>
    @endif
    @endauth

    {{-- ─── GRID: ALL tab ─────────────────────────────────────────── --}}
    @if($tab === 'all')
        @if($resources->isEmpty())
            <div class="resource-empty">
                <div class="resource-empty__icon">📭</div>
                <h3>No resources found</h3>
                <p>Try adjusting your filters or search terms.</p>
            </div>
        @else
            <div class="resource-grid">
                @foreach($resources as $resource)
                    @include('resources._card', ['resource' => $resource, 'savedIds' => $savedIds])
                @endforeach
            </div>
            <div class="resource-pagination">
                {{ $resources->links() }}
            </div>
        @endif

    {{-- ─── GRID: SAVED tab ─────────────────────────────────────── --}}
    @elseif($tab === 'saved')
        @if($savedResources && $savedResources->isEmpty())
            <div class="resource-empty">
                <div class="resource-empty__icon">🔖</div>
                <h3>No saved resources yet</h3>
                <p>Click the ♡ on any resource card to bookmark it here.</p>
            </div>
        @elseif($savedResources)
            <div class="resource-grid">
                @foreach($savedResources as $resource)
                    @include('resources._card', ['resource' => $resource, 'savedIds' => $savedIds])
                @endforeach
            </div>
            <div class="resource-pagination">
                {{ $savedResources->links() }}
            </div>
        @endif

    {{-- ─── GRID: PROPOSALS tab ─────────────────────────────────── --}}
    @elseif($tab === 'proposals')
        @if($proposals && $proposals->isEmpty())
            <div class="resource-empty">
                <div class="resource-empty__icon">📝</div>
                <h3>No proposals yet</h3>
                <p>Submit a resource for admin review using the button above.</p>
            </div>
        @elseif($proposals)
            <div class="resource-grid">
                @foreach($proposals as $resource)
                    @include('resources._card', ['resource' => $resource, 'savedIds' => $savedIds, 'showStatus' => true])
                @endforeach
            </div>
            <div class="resource-pagination">
                {{ $proposals->links() }}
            </div>
        @endif
    @endif

</div>

<style>
/* ═══════════════════════════════════════════════════════════════════════════
   LEARNING HUB — Design System
═══════════════════════════════════════════════════════════════════════════ */

/* ── Hero ──────────────────────────────────────────────────────────────── */
.resource-hero {
    position: relative; overflow: hidden;
    background: linear-gradient(135deg, #003a1c 0%, #005f2e 40%, #00874a 70%, #1a6fb0 100%);
    padding: 80px 32px 60px;
    text-align: center;
}
.resource-hero__bg {
    position: absolute; inset: 0;
    background: radial-gradient(ellipse at 20% 50%, rgba(0,135,74,0.3) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 30%, rgba(26,111,176,0.25) 0%, transparent 60%);
    pointer-events: none;
}
.resource-hero__particles {
    position: absolute; inset: 0; pointer-events: none;
}
.resource-hero__content { position: relative; z-index: 2; max-width: 800px; margin: 0 auto; }
.resource-hero__badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,0.12); backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff; font-size: 13px; font-weight: 600; letter-spacing: 1px;
    text-transform: uppercase; padding: 6px 16px; border-radius: 99px;
    margin-bottom: 20px;
}
.resource-hero__title {
    font-family: 'Montserrat', sans-serif;
    font-size: clamp(36px, 5vw, 60px); font-weight: 800;
    color: #fff; line-height: 1.1; margin-bottom: 16px;
}
.resource-hero__title--accent {
    background: linear-gradient(90deg, #FF8300, #FFD700);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}
.resource-hero__subtitle {
    font-size: 18px; color: rgba(255,255,255,0.8);
    max-width: 560px; margin: 0 auto 12px;
}
.resource-hero__role-tag {
    font-size: 14px; color: rgba(255,255,255,0.7);
    margin-bottom: 24px;
}
.resource-hero__stats {
    display: flex; justify-content: center; gap: 40px;
    margin-top: 28px;
}
.resource-hero__stat { text-align: center; }
.resource-hero__stat-num {
    display: block; font-size: 32px; font-weight: 800; color: #FF8300;
    font-family: 'Montserrat', sans-serif;
}
.resource-hero__stat-label {
    font-size: 12px; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 1px;
}

/* ── Page wrap ─────────────────────────────────────────────────────────── */
.resource-page { max-width: 1280px; margin: 0 auto; padding: 0 24px 80px; }

/* ── Flash ─────────────────────────────────────────────────────────────── */
.flash-success {
    display: flex; align-items: center; justify-content: space-between;
    background: rgba(0,135,74,0.1); border: 1px solid rgba(0,135,74,0.3);
    color: #007934; border-radius: 10px; padding: 12px 16px;
    margin: 20px 0 0; font-size: 14px;
}
.flash-success button { background: none; border: none; cursor: pointer; color: #007934; font-size: 16px; }

/* ── Tabs ──────────────────────────────────────────────────────────────── */
.resource-tabs {
    display: flex; flex-wrap: wrap; gap: 8px;
    margin: 28px 0 0; border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0;
}
.resource-tab {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 20px; border-radius: 10px 10px 0 0;
    font-size: 14px; font-weight: 600; text-decoration: none;
    color: #4b5563;
    border: 1px solid transparent; border-bottom: none;
    transition: all 0.2s;
    margin-bottom: -2px;
}
.resource-tab:hover { color: #007934; background: #f3f4f6; }
.resource-tab--active {
    color: #007934; background: #fff;
    border: 2px solid #e5e7eb; border-bottom: 2px solid #fff;
    font-weight: 700;
}
.resource-tab--pending { color: #FF8300; }
.resource-tab__badge {
    background: rgba(255,131,0,0.15); color: #FF8300;
    border-radius: 99px; font-size: 11px; font-weight: 700;
    padding: 1px 7px;
}
.resource-tab__badge--warn { background: rgba(255,131,0,0.25); }

/* ── Filters ───────────────────────────────────────────────────────────── */
.resource-filters {
    display: flex; flex-direction: column; gap: 12px;
    margin: 24px 0 20px;
}
.resource-search-wrap {
    position: relative; display: flex; align-items: center;
}
.resource-search-icon {
    position: absolute; left: 14px; font-size: 16px; pointer-events: none;
    color: #9ca3af;
}
.resource-search-input {
    width: 100%; padding: 12px 16px 12px 42px;
    background: #ffffff; border: 1px solid #d1d5db;
    border-radius: 10px; color: #1f2937; font-size: 14px;
    outline: none; transition: border 0.2s;
}
.resource-search-input::placeholder { color: #9ca3af; }
.resource-search-input:focus { border-color: #007934; box-shadow: 0 0 0 3px rgba(0, 121, 52, 0.1); }

.resource-filter-chips {
    display: flex; flex-wrap: wrap; gap: 8px;
}
.resource-chip {
    padding: 6px 16px; border-radius: 99px; font-size: 13px; font-weight: 500;
    cursor: pointer; border: 1px solid #e5e7eb;
    background: #f9fafb; color: #4b5563;
    transition: all 0.2s;
}
.resource-chip:hover { color: #111827; background: #f3f4f6; border-color: #d1d5db; }
.resource-chip--active { background: #007934; color: #fff; border-color: #007934; }
.resource-chip--active:hover { color: #fff; background: #006028; border-color: #006028; }

/* Area chip colors */
.resource-chip--area-stem        { border-color: rgba(0,121,52,0.4); }
.resource-chip--area-innovation  { border-color: rgba(255,131,0,0.4); }
.resource-chip--area-ai          { border-color: rgba(99,102,241,0.4); }
.resource-chip--area-robotics    { border-color: rgba(14,165,233,0.4); }
.resource-chip--area-design      { border-color: rgba(236,72,153,0.4); }
.resource-chip--area-programming { border-color: rgba(139,92,246,0.4); }
.resource-chip--area-math        { border-color: rgba(245,158,11,0.4); }
.resource-chip--area-science     { border-color: rgba(20,184,166,0.4); }
.resource-chip--area-general     { border-color: rgba(107,114,128,0.4); }

/* ── Action bar ────────────────────────────────────────────────────────── */
.resource-action-bar {
    display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 24px; align-items: center;
}
.resource-btn-propose {
    display: inline-flex; align-items: center; gap: 6px;
    background: linear-gradient(135deg, #007934, #00a04a);
    color: #fff; padding: 10px 20px; border-radius: 10px;
    font-size: 14px; font-weight: 600; text-decoration: none;
    transition: all 0.2s; box-shadow: 0 4px 15px rgba(0,121,52,0.15);
}
.resource-btn-propose:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,121,52,0.25); }
.resource-btn-propose span { font-size: 18px; line-height: 1; }
.resource-btn-admin {
    display: inline-flex; align-items: center; gap: 8px;
    background: #f3f4f6; border: 1px solid #e5e7eb;
    color: #4b5563; padding: 10px 20px; border-radius: 10px;
    font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.2s;
}
.resource-btn-admin:hover { background: #e5e7eb; color: #111827; }
.resource-btn-admin__badge {
    background: rgba(255,131,0,0.15); color: #FF8300;
    font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 99px;
}

/* ── Resource grid ─────────────────────────────────────────────────────── */
.resource-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

/* ── Resource card ─────────────────────────────────────────────────────── */
.resource-card {
    background: #ffffff; border: 1px solid #e5e7eb;
    border-radius: 16px; overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex; flex-direction: column;
    position: relative;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
}
.resource-card:hover {
    background: #ffffff;
    border-color: rgba(255, 131, 0, 0.5);
    transform: translateY(-5px) scale(1.015);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
}
.resource-card__thumbnail {
    width: 100%; aspect-ratio: 16/7; object-fit: cover; background: #f3f4f6;
}
.resource-card__thumbnail-placeholder {
    width: 100%; aspect-ratio: 16/7;
    display: flex; align-items: center; justify-content: center;
    font-size: 40px;
}
.resource-card__body { padding: 16px; flex: 1; display: flex; flex-direction: column; }
.resource-card__badges { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 10px; }
.resource-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;
    padding: 3px 9px; border-radius: 99px;
}

/* Area badge colors */
.resource-badge--stem        { background: rgba(0,121,52,0.1);   color: #007934; border: 1px solid rgba(0,121,52,0.2); }
.resource-badge--innovation  { background: rgba(255,131,0,0.1);   color: #d97706; border: 1px solid rgba(255,131,0,0.2); }
.resource-badge--ai          { background: rgba(99,102,241,0.1);  color: #4f46e5; border: 1px solid rgba(99,102,241,0.2); }
.resource-badge--robotics    { background: rgba(14,165,233,0.1);  color: #0284c7; border: 1px solid rgba(14,165,233,0.2); }
.resource-badge--design      { background: rgba(236,72,153,0.1);  color: #db2777; border: 1px solid rgba(236,72,153,0.2); }
.resource-badge--programming { background: rgba(139,92,246,0.1);  color: #7c3aed; border: 1px solid rgba(139,92,246,0.2); }
.resource-badge--math        { background: rgba(245,158,11,0.1);  color: #d97706; border: 1px solid rgba(245,158,11,0.2); }
.resource-badge--science     { background: rgba(20,184,166,0.1);  color: #0d9488; border: 1px solid rgba(20,184,166,0.2); }
.resource-badge--general     { background: rgba(107,114,128,0.1); color: #4b5563; border: 1px solid rgba(107,114,128,0.2); }

.resource-badge--type {
    background: #f3f4f6; color: #4b5563;
    border: 1px solid #e5e7eb;
}
.resource-badge--pending { background: rgba(255,131,0,0.15); color: #d97706; border: 1px solid rgba(255,131,0,0.25); }
.resource-badge--published { background: rgba(0,121,52,0.1); color: #007934; border: 1px solid rgba(0,121,52,0.2); }

.resource-card__title {
    font-size: 15px; font-weight: 700; color: #111827; line-height: 1.4;
    margin-bottom: 6px; text-decoration: none; display: block;
}
.resource-card__title:hover { color: #FF8300; }
.resource-card__source {
    font-size: 12px; color: #6b7280; margin-bottom: 8px;
}
.resource-card__desc {
    font-size: 13px; color: #4b5563; line-height: 1.5;
    flex: 1; margin-bottom: 12px;
    display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
}
.resource-card__footer {
    display: flex; align-items: center; justify-content: space-between; gap: 8px;
    margin-top: auto; padding-top: 12px; border-top: 1px solid #f3f4f6;
}
.resource-card__link {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px; font-weight: 600; color: #007934; text-decoration: none;
    transition: color 0.2s;
}
.resource-card__link:hover { color: #FF8300; }
.resource-card__save-btn {
    background: none; border: 1px solid #d1d5db;
    color: #6b7280; border-radius: 8px;
    padding: 6px 10px; font-size: 14px; cursor: pointer; transition: all 0.2s;
}
.resource-card__save-btn:hover { border-color: #FF8300; color: #FF8300; }
.resource-card__save-btn--saved { border-color: #FF8300; color: #FF8300; background: rgba(255,131,0,0.05); }

/* ── Thumbnail placeholder gradient by area ─────────────────────────────── */
.resource-card__thumbnail-placeholder.area-stem        { background: linear-gradient(135deg, #003a1c, #005f2e); }
.resource-card__thumbnail-placeholder.area-innovation  { background: linear-gradient(135deg, #7a3c00, #b55a00); }
.resource-card__thumbnail-placeholder.area-ai          { background: linear-gradient(135deg, #1e1b6a, #3730a3); }
.resource-card__thumbnail-placeholder.area-robotics    { background: linear-gradient(135deg, #0c3a5a, #0369a1); }
.resource-card__thumbnail-placeholder.area-design      { background: linear-gradient(135deg, #6b0033, #9d174d); }
.resource-card__thumbnail-placeholder.area-programming { background: linear-gradient(135deg, #3b0764, #6d28d9); }
.resource-card__thumbnail-placeholder.area-math        { background: linear-gradient(135deg, #78350f, #b45309); }
.resource-card__thumbnail-placeholder.area-science     { background: linear-gradient(135deg, #042f2e, #0f766e); }
.resource-card__thumbnail-placeholder.area-general     { background: linear-gradient(135deg, #1f2937, #374151); }

/* ── Empty state ───────────────────────────────────────────────────────── */
.resource-empty {
    text-align: center; padding: 80px 20px; color: #6b7280;
}
.resource-empty__icon { font-size: 64px; margin-bottom: 16px; }
.resource-empty h3 { font-size: 20px; color: #1f2937; margin-bottom: 8px; }
.resource-empty p { font-size: 14px; }

/* ── Pagination ────────────────────────────────────────────────────────── */
.resource-pagination { display: flex; justify-content: center; }
.resource-pagination nav { display: flex; gap: 4px; }
.resource-pagination .page-link, .resource-pagination [aria-label] {
    padding: 8px 14px; border-radius: 8px; font-size: 14px;
    background: #ffffff; border: 1px solid #e5e7eb;
    color: #4b5563; text-decoration: none; transition: all 0.2s;
}
.resource-pagination .active span,
.resource-pagination [aria-current="page"] span {
    background: #007934; border-color: #007934; color: #fff;
    padding: 8px 14px; border-radius: 8px; display: block;
}
</style>

<script>
function resourceHub() {
    return {
        searchTimer: null,
        init() {
            this.spawnParticles();
        },
        debounceSubmit() {
            clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(() => {
                document.getElementById('resource-filter-form').submit();
            }, 500);
        },
        spawnParticles() {
            const container = document.getElementById('resourceParticles');
            if (!container) return;
            const icons = ['⚛️','🔬','🤖','💡','🚀','🧬','📡','🔭','⚙️','🧩'];
            for (let i = 0; i < 12; i++) {
                const el = document.createElement('div');
                el.style.cssText = `
                    position:absolute; font-size:${16 + Math.random()*16}px;
                    left:${Math.random()*100}%; top:${Math.random()*100}%;
                    opacity:${0.08 + Math.random()*0.12};
                    animation: floatParticle ${6 + Math.random()*8}s ease-in-out infinite;
                    animation-delay:${Math.random()*-10}s;
                    pointer-events:none; user-select:none;
                `;
                el.textContent = icons[i % icons.length];
                container.appendChild(el);
            }
        }
    };
}
</script>

<style>
@keyframes floatParticle {
    0%,100% { transform: translateY(0) rotate(0deg); }
    50%      { transform: translateY(-20px) rotate(10deg); }
}
</style>
@endsection
