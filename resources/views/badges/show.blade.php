@extends('layouts.app')

@section('header-title', 'Badge Details')
@section('header-subtitle', 'Understand the criteria and check your qualification status')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <a href="{{ route('badges.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-ans-dark-green transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Back to Badges Gallery</span>
        </a>
    </div>

    <!-- Main Badge Show Card -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden mb-8">
        <!-- Top Colored Header Block -->
        <div class="h-32 relative flex items-end px-8 pb-4" style="background: linear-gradient(135deg, {{ $badge->color }} 0%, {{ $badge->color }}dd 60%, {{ $badge->color }}88 100%)">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle, white 10%, transparent 11%); background-size: 12px 12px;"></div>
            
            <!-- Floating Badge Icon -->
            <div class="absolute -bottom-10 left-8 w-24 h-24 rounded-3xl bg-white border-4 border-white shadow-2xl flex items-center justify-center text-5xl">
                {{ $badge->icon }}
            </div>
        </div>

        <div class="pt-16 px-8 pb-8">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-2.5 mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider" 
                              style="background-color: {{ $badge->color }}20; color: {{ $badge->color }}">
                            {{ str_replace('_', ' ', $badge->category) }}
                        </span>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">{{ $badge->difficulty }} Level</span>
                    </div>
                    <h3 class="text-2xl font-heading font-extrabold text-gray-800">{{ $badge->name }}</h3>
                </div>

                @if($isEarned)
                    <span class="px-4 py-2 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Earned & Active</span>
                    </span>
                @else
                    <span class="px-4 py-2 bg-ans-orange/10 text-ans-orange text-xs font-bold rounded-xl flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Locked</span>
                    </span>
                @endif
            </div>

            <!-- Description -->
            <p class="text-gray-600 text-sm leading-relaxed mb-6">{{ $badge->description }}</p>

            <!-- Qualification Criteria -->
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 mb-6">
                <h4 class="font-heading font-bold text-gray-800 text-sm mb-3">Qualification Requirement</h4>
                @if($badge->criteria_type === 'quiz')
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">Theoretical/Practical Micro-Quiz</p>
                            <p class="text-[11px] text-gray-500 mt-0.5">Must answer a 5-question multi-choice exam. Passing score is {{ $quiz->passing_score ?? 80 }}% or higher.</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">Activity Checklist</p>
                            <p class="text-[11px] text-gray-500 mt-0.5">Assigned manually by platform administrator based on classroom usage metrics.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Qualification Box / Results -->
            @if($isEarned)
                <div class="p-6 border border-emerald-200 bg-emerald-50/50 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h4 class="font-heading font-bold text-emerald-800 text-sm mb-1">🎉 You have earned this badge!</h4>
                        <p class="text-xs text-emerald-700">Awarded on {{ $earnedPivot->earned_at ? \Carbon\Carbon::parse($earnedPivot->earned_at)->format('M d, Y') : now()->format('M d, Y') }} with a score of {{ $earnedPivot->score }}%.</p>
                    </div>
                    @if($badge->criteria_type === 'quiz' && $quiz)
                        <a href="{{ route('quizzes.show', $quiz) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                            Review Answers
                        </a>
                    @endif
                </div>
            @else
                <div class="p-6 border border-gray-200 bg-gray-50/50 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                    @if($badge->criteria_type === 'quiz' && $quiz)
                        <div>
                            <h4 class="font-heading font-bold text-gray-800 text-sm mb-1">Cuestionario Disponible</h4>
                            <p class="text-xs text-gray-500">Take the micro-quiz to demonstrate your proficiency and unlock this badge.</p>
                        </div>
                        <a href="{{ route('quizzes.show', $quiz) }}" class="px-5 py-2.5 bg-ans-dark-green text-white text-xs font-bold rounded-xl hover:bg-ans-seal-green transition-all shadow-md">
                            Start Quiz
                        </a>
                    @else
                        <div>
                            <h4 class="font-heading font-bold text-gray-800 text-sm mb-1">Automatic/Manual Assignment</h4>
                            <p class="text-xs text-gray-500">This badge is certified when platform events (lesson plan creation or prompt shares) meet criteria.</p>
                        </div>
                        <span class="text-xs text-gray-400 font-semibold italic">Requires Review</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
