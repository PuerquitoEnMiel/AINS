@extends('layouts.app')

@section('header-title', 'EdTech Badges')
@section('header-subtitle', 'Earn micro-certifications and level up your classroom AI integration')

@section('content')
<style>
    .badge-card {
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid rgba(229, 231, 235, 0.5);
    }
    .badge-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 30px rgba(0, 121, 52, 0.05), 0 4px 12px rgba(255, 131, 0, 0.03);
    }
    .glow-bronze:hover { border-color: rgba(205, 127, 50, 0.3) !important; }
    .glow-silver:hover { border-color: rgba(192, 192, 192, 0.4) !important; }
    .glow-gold:hover { border-color: rgba(255, 215, 0, 0.4) !important; }
</style>

<!-- Progress Banner -->
<div class="bg-gradient-to-br from-ans-dark-green to-ans-seal-green rounded-3xl p-6 md:p-8 mb-8 text-white relative overflow-hidden shadow-lg">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-10 left-10 w-48 h-48 bg-ans-orange/10 rounded-full blur-2xl pointer-events-none"></div>
    
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h3 class="text-2xl font-heading font-extrabold mb-2">Your Certification Path</h3>
            <p class="text-white/70 text-sm max-w-md">Envía evidencias para desbloquear prestigiosas credenciales profesionales.</p>
        </div>
        
        <div class="flex-1 max-w-md w-full bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/10">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-ans-light-green">Platform Adherence</span>
                <span class="text-sm font-bold">{{ $earnedBadgesCount }} / {{ $totalBadgesCount }} Badges</span>
            </div>
            <div class="w-full bg-white/10 rounded-full h-3 overflow-hidden">
                <div class="bg-gradient-to-r from-ans-orange to-yellow-400 h-full rounded-full transition-all duration-1000" style="width: {{ $progressPercent }}%"></div>
            </div>
            <div class="flex justify-between mt-2 text-[10px] text-white/50">
                <span>Novice Explorer</span>
                <span>{{ $progressPercent }}% Complete</span>
                <span>EdTech Champion</span>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Gallery -->
<div class="mb-10">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h3 class="text-lg font-heading font-bold text-gray-900">Available Certifications</h3>
            <p class="text-xs text-gray-500">Filter by category or difficulty level</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('badge-suggestions.create') }}" class="px-4 py-2 bg-ans-dark-green text-white text-xs font-semibold rounded-xl hover:bg-ans-seal-green transition-all shadow-sm">
                💡 Sugerir Insignia
            </a>
            
            <!-- Filter Tabs (Vanilla dynamic filtering via simple JS) -->
            <div class="flex flex-wrap gap-2" id="filter-buttons">
                <button onclick="filterBadges('all')" class="filter-btn px-4 py-1.5 rounded-full text-xs font-semibold bg-ans-dark-green text-white shadow-sm transition-all">All</button>
                <button onclick="filterBadges('tool_mastery')" class="filter-btn px-4 py-1.5 rounded-full text-xs font-medium bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 transition-all">Tool Mastery</button>
                <button onclick="filterBadges('ai_safety')" class="filter-btn px-4 py-1.5 rounded-full text-xs font-medium bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 transition-all">AI Safety</button>
                <button onclick="filterBadges('pedagogy')" class="filter-btn px-4 py-1.5 rounded-full text-xs font-medium bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 transition-all">Pedagogy</button>
                <button onclick="filterBadges('platform')" class="filter-btn px-4 py-1.5 rounded-full text-xs font-medium bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 transition-all">Platform</button>
            </div>
        </div>
    </div>

    <!-- Badge Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="badge-grid">
        @forelse($badges as $badge)
            @php
                $earnedBadge = $earnedBadgesMap->get($badge->id);
                $isEarned = !!$earnedBadge;
                $earnedPivot = $isEarned ? $earnedBadge->pivot : null;
                $difficultyColor = match($badge->difficulty) {
                    'bronze' => '#CD7F32',
                    'silver' => '#9CA3AF',
                    'gold' => '#D97706',
                    default => '#6B7280'
                };
                $glowClass = 'glow-' . $badge->difficulty;
                $mandatoryBorder = $badge->is_mandatory ? 'border-2 border-red-500/30 ring-2 ring-red-500/10' : '';

                // Expiry status computation
                $expiry = null;
                if ($isEarned && $earnedPivot?->earned_at) {
                    $expiry = $badge->expiryStatusFor(\Carbon\Carbon::parse($earnedPivot->earned_at));
                }
            @endphp
            <div class="badge-card bg-white rounded-2xl p-6 {{ $glowClass }} {{ $mandatoryBorder }} flex flex-col justify-between relative overflow-hidden" 
                 data-category="{{ $badge->category }}">
                
                {{-- Floating expiry status badge (top-right corner) --}}
                @if($isEarned && $expiry)
                    @if($expiry['status'] === 'expired')
                        <div class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-red-100 text-red-600 border border-red-200 animate-pulse">
                            ⚠ Expirada
                        </div>
                    @elseif($expiry['status'] === 'warning')
                        <div class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wider border animate-pulse"
                             style="background-color: #FF830015; color: #FF8300; border-color: #FF830040;">
                            ⏰ {{ $expiry['days_remaining'] }}d restantes
                        </div>
                    @elseif($expiry['status'] === 'active')
                        <div class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-200">
                            ✓ Vigente
                        </div>
                    @elseif($expiry['status'] === 'permanent')
                        <div class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider bg-gray-50 text-gray-400 border border-gray-200">
                            ∞ Permanente
                        </div>
                    @endif
                @endif

                <!-- Category/Difficulty Top Row -->
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider" 
                              style="background-color: {{ $badge->color }}20; color: {{ $badge->color }}">
                            {{ str_replace('_', ' ', $badge->category) }}
                        </span>
                        @if($badge->is_mandatory)
                        <span class="px-2 py-0.5 rounded bg-red-100 text-red-700 text-[9px] font-extrabold uppercase tracking-wider">
                            Obligatorio
                        </span>
                        @endif
                    </div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest" style="color: {{ $difficultyColor }}">
                        {{ $badge->difficulty }}
                    </span>
                </div>

                <!-- Insignia Info -->
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-4xl shadow-md border shrink-0 bg-gray-50 transition-all border-gray-100 overflow-hidden">
                        @if($badge->image_path)
                            <img src="{{ asset('storage/' . $badge->image_path) }}" class="w-full h-full object-cover">
                        @else
                            {{ $badge->icon }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-heading font-bold text-gray-800 text-base truncate mb-1">{{ $badge->name }}</h4>
                        <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ $badge->description }}</p>
                    </div>
                </div>

                {{-- Expiry Progress Bar (only for earned badges with validity) --}}
                @if($isEarned && $expiry && $expiry['status'] !== 'permanent')
                    <div class="mb-4 px-1">
                        @php
                            $barColor = match($expiry['status']) {
                                'active'  => 'from-emerald-400 to-emerald-500',
                                'warning' => 'from-amber-400 to-orange-500',
                                'expired' => 'from-red-400 to-red-500',
                                default   => 'from-gray-300 to-gray-400',
                            };
                            $barBg = match($expiry['status']) {
                                'warning' => 'bg-orange-100',
                                'expired' => 'bg-red-100',
                                default   => 'bg-gray-100',
                            };
                        @endphp
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-[10px] font-semibold text-gray-500">Validez: {{ $badge->validityLabel() }}</span>
                            @if($expiry['status'] === 'expired')
                                <span class="text-[10px] font-bold text-red-500">Expirada</span>
                            @else
                                <span class="text-[10px] font-bold" style="color: {{ $expiry['status'] === 'warning' ? '#FF8300' : '#10B981' }}">
                                    {{ $expiry['days_remaining'] }} días restantes
                                </span>
                            @endif
                        </div>
                        <div class="w-full {{ $barBg }} rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r {{ $barColor }} h-full rounded-full transition-all duration-1000 ease-out"
                                 style="width: {{ min($expiry['progress'], 100) }}%"></div>
                        </div>
                        @if($expiry['expires_at'])
                            <div class="text-[9px] text-gray-400 mt-1">
                                Expira: {{ $expiry['expires_at']->format('d M Y') }}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Status & Action Block -->
                <div class="pt-4 border-t border-gray-100 flex flex-col gap-3">
                    @if($isEarned)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5 text-emerald-600 text-xs font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Earned{{ $earnedPivot?->score ? " (Score: {$earnedPivot->score}%)" : '' }}</span>
                            </div>
                            <a href="{{ route('badges.show', $badge->slug) }}" class="text-xs text-ans-dark-green font-semibold hover:underline">Review Detail</a>
                        </div>

                        {{-- Warning CTA: Re-certification needed --}}
                        @if($expiry && $expiry['status'] === 'warning')
                            <a href="{{ route('badges.show', $badge->slug) }}" 
                               class="w-full text-center px-3.5 py-2 rounded-xl text-xs font-bold transition-all shadow-sm border"
                               style="background-color: #FF830012; color: #FF8300; border-color: #FF830030;"
                               onmouseenter="this.style.backgroundColor='#FF830025'" onmouseleave="this.style.backgroundColor='#FF830012'">
                                🔄 Próxima a expirar — Re-certifícate
                            </a>
                        @elseif($expiry && $expiry['status'] === 'expired')
                            <a href="{{ route('badges.show', $badge->slug) }}" 
                               class="w-full text-center px-3.5 py-2 bg-red-50 text-red-600 border border-red-200 rounded-xl text-xs font-bold hover:bg-red-100 transition-all shadow-sm">
                                📋 Certificación expirada — Enviar nueva evidencia
                            </a>
                        @endif
                    @else
                        <div class="flex items-center justify-between">
                            <div class="text-[10px] font-semibold text-gray-400">
                                Status: <span class="text-ans-orange">Locked 🔓</span>
                            </div>
                            
                            <a href="{{ route('badges.show', $badge->slug) }}" class="px-3.5 py-1.5 bg-ans-dark-green text-white text-xs font-bold rounded-xl hover:bg-ans-seal-green transition-all shadow-sm">
                                Ver Detalles
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-sm">
                <span class="text-5xl mb-4 block">🏅</span>
                <h4 class="font-heading font-bold text-gray-800 text-lg mb-2">No Badges Active</h4>
                <p class="text-sm text-gray-400 max-w-sm mx-auto">Please check back later or ask an administrator to initialize the credentials catalog.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
    function filterBadges(category) {
        // Toggle active button style
        const buttons = document.querySelectorAll('.filter-btn');
        buttons.forEach(btn => {
            btn.className = "filter-btn px-4 py-1.5 rounded-full text-xs font-medium bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 transition-all";
        });
        
        event.currentTarget.className = "filter-btn px-4 py-1.5 rounded-full text-xs font-semibold bg-ans-dark-green text-white shadow-sm transition-all";

        // Show/hide cards
        const cards = document.querySelectorAll('#badge-grid > div');
        cards.forEach(card => {
            if (category === 'all' || card.dataset.category === category) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection
