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
            <p class="text-white/70 text-sm max-w-md">Complete micro-quizzes based on educational technologies to unlock prestigious professional credentials.</p>
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
        
        <!-- Filter Tabs (Vanilla dynamic filtering via simple JS) -->
        <div class="flex flex-wrap gap-2" id="filter-buttons">
            <button onclick="filterBadges('all')" class="filter-btn px-4 py-1.5 rounded-full text-xs font-semibold bg-ans-dark-green text-white shadow-sm transition-all">All</button>
            <button onclick="filterBadges('tool_mastery')" class="filter-btn px-4 py-1.5 rounded-full text-xs font-medium bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 transition-all">Tool Mastery</button>
            <button onclick="filterBadges('ai_safety')" class="filter-btn px-4 py-1.5 rounded-full text-xs font-medium bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 transition-all">AI Safety</button>
            <button onclick="filterBadges('pedagogy')" class="filter-btn px-4 py-1.5 rounded-full text-xs font-medium bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 transition-all">Pedagogy</button>
            <button onclick="filterBadges('platform')" class="filter-btn px-4 py-1.5 rounded-full text-xs font-medium bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 transition-all">Platform</button>
        </div>
    </div>

    <!-- Badge Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="badge-grid">
        @forelse($badges as $badge)
            @php
                $isEarned = auth()->user() && auth()->user()->hasBadge($badge->slug);
                $earnedPivot = $isEarned ? auth()->user()->badges()->where('slug', $badge->slug)->first()->pivot : null;
                $difficultyColor = match($badge->difficulty) {
                    'bronze' => '#CD7F32',
                    'silver' => '#9CA3AF',
                    'gold' => '#D97706',
                    default => '#6B7280'
                };
                $glowClass = 'glow-' . $badge->difficulty;
            @endphp
            <div class="badge-card bg-white rounded-2xl p-6 {{ $glowClass }} flex flex-col justify-between relative overflow-hidden" 
                 data-category="{{ $badge->category }}">
                
                <!-- Category/Difficulty Top Row -->
                <div class="flex justify-between items-center mb-4">
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider" 
                          style="background-color: {{ $badge->color }}20; color: {{ $badge->color }}">
                        {{ str_replace('_', ' ', $badge->category) }}
                    </span>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest" style="color: {{ $difficultyColor }}">
                        {{ $badge->difficulty }}
                    </span>
                </div>

                <!-- Insignia Info -->
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-4xl shadow-md border shrink-0 bg-gray-50 transition-all border-gray-100">
                        {{ $badge->icon }}
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-heading font-bold text-gray-800 text-base truncate mb-1">{{ $badge->name }}</h4>
                        <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ $badge->description }}</p>
                    </div>
                </div>

                <!-- Status & Action Block -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    @if($isEarned)
                        <div class="flex items-center gap-1.5 text-emerald-600 text-xs font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Earned (Score: {{ $earnedPivot->score }}%)</span>
                        </div>
                        <a href="{{ route('badges.show', $badge->slug) }}" class="text-xs text-ans-dark-green font-semibold hover:underline">Review Detail</a>
                    @else
                        <div class="text-[10px] font-semibold text-gray-400">
                            Status: <span class="text-ans-orange">Locked 🔓</span>
                        </div>
                        
                        @if($badge->criteria_type === 'quiz' && $badge->quiz)
                            <a href="{{ route('badges.show', $badge->slug) }}" class="px-3.5 py-1.5 bg-ans-dark-green text-white text-xs font-bold rounded-xl hover:bg-ans-seal-green transition-all shadow-sm">
                                Take Quiz
                            </a>
                        @else
                            <a href="{{ route('badges.show', $badge->slug) }}" class="px-3.5 py-1.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-xl hover:bg-gray-200 transition-all">
                                View Details
                            </a>
                        @endif
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
