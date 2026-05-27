@extends('layouts.app')

@section('header-title', $tool->name)
@section('header-subtitle', $tool->categoryRelation ? $tool->categoryRelation->icon . ' ' . $tool->categoryRelation->name : 'Tool Details')

@section('content')

<!-- Minimalist Back Button -->
<div class="mb-6">
    <a href="/" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-ans-dark-green transition-colors">
        <span>← Volver al Directorio</span>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Tool Hero Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="h-3 bg-gradient-to-r from-ans-dark-green via-ans-light-green to-ans-orange"></div>
            <div class="p-8">
                <div class="flex items-start gap-5 mb-6">
                    @if($tool->logo_url)
                        <img src="{{ $tool->logo_url }}" alt="{{ $tool->name }}" class="w-20 h-20 rounded-2xl object-cover border border-gray-200 shadow-sm">
                    @else
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-ans-dark-green to-ans-light-green flex items-center justify-center text-white font-bold text-2xl shadow-sm">{{ substr($tool->name, 0, 2) }}</div>
                    @endif
                    <div class="flex-1">
                        <h1 class="text-2xl font-heading font-extrabold text-gray-900">{{ $tool->name }}</h1>
                        <div class="flex items-center gap-3 mt-2 flex-wrap">
                            @if($tool->categoryRelation)
                                <span class="px-3 py-1 bg-gray-100 rounded-lg text-xs font-medium text-gray-600">{{ $tool->categoryRelation->icon }} {{ $tool->categoryRelation->name }}</span>
                            @endif
                            @if($tool->is_google_workspace)
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium">Google Workspace</span>
                            @endif
                            @if($tool->featured)
                                <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-xs font-medium flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    Featured
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <p class="text-gray-700 leading-relaxed mb-6">{{ $tool->description }}</p>

                <div class="flex items-center gap-4 flex-wrap">
                    <a href="{{ $tool->url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-6 py-3 bg-ans-dark-green text-white rounded-xl font-semibold hover:bg-ans-seal-green transition-all shadow-md hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        Open Tool
                    </a>
                    @auth
                    <button onclick="toggleFavorite({{ $tool->id }})" id="fav-btn-{{ $tool->id }}" class="inline-flex items-center gap-2 px-5 py-3 border-2 rounded-xl font-semibold transition-all {{ $isFavorited ? 'border-red-300 bg-red-50 text-red-500' : 'border-gray-200 bg-white text-gray-600 hover:border-red-300 hover:text-red-500' }}">
                        <svg class="w-5 h-5" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        <span id="fav-text-{{ $tool->id }}">{{ $isFavorited ? 'Favorited' : 'Add to Favorites' }}</span>
                    </button>
                    @endauth
                </div>
            </div>
        </div>

        <!-- AI Insights Section -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-ans-dark-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                        <h3 class="text-lg font-heading font-bold text-gray-800">AI Insights & Analysis</h3>
                    </div>
                    @if(Auth::check() && Auth::user()->isAdmin())
                        <form method="POST" action="{{ route('admin.tools.generateInsight', $tool) }}">
                            @csrf
                            @if($tool->reviews->count() >= 3)
                                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-ans-dark-green to-ans-light-green text-white rounded-xl text-xs font-semibold hover:shadow-md transition-all">
                                    {{ $tool->insight ? 'Regenerate Insights' : 'Generate Insights' }}
                                </button>
                            @else
                                <button type="button" disabled class="px-4 py-2 bg-gray-100 text-gray-400 rounded-xl text-xs font-semibold cursor-not-allowed">
                                    Requires 3 Reviews
                                </button>
                            @endif
                        </form>
                    @endif
                </div>

                @if($tool->insight)
                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50/50 rounded-xl border border-gray-100/50">
                            <p class="text-sm text-gray-700 leading-relaxed font-medium">
                                "{{ $tool->insight->summary }}"
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Pros -->
                            <div class="space-y-2">
                                <h4 class="text-xs font-bold text-emerald-600 uppercase tracking-wider flex items-center gap-1">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Key Advantages
                                </h4>
                                <ul class="space-y-1.5">
                                    @foreach($tool->insight->pros as $pro)
                                        <li class="text-xs text-gray-600 flex items-start gap-1.5">
                                            <span class="text-emerald-500">✓</span>
                                            <span>{{ $pro }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Cons -->
                            <div class="space-y-2">
                                <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider flex items-center gap-1">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Limitations or Cons
                                </h4>
                                <ul class="space-y-1.5">
                                    @foreach($tool->insight->cons as $con)
                                        <li class="text-xs text-gray-600 flex items-start gap-1.5">
                                            <span class="text-amber-500">•</span>
                                            <span>{{ $con }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-50 flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Best for:</span>
                                @foreach($tool->insight->best_for_grades as $grade)
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded-md text-[10px] font-medium">{{ $grade }}</span>
                                @endforeach
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Use Cases:</span>
                                @foreach($tool->insight->best_use_cases as $case)
                                    <span class="px-2 py-0.5 bg-purple-50 text-purple-600 rounded-md text-[10px] font-medium">{{ $case }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="text-[10px] text-gray-400 text-right mt-2">
                            Automatically generated with Gemini • Based on {{ $tool->insight->review_count_at_generation }} reviews • Updated {{ $tool->insight->generated_at->diffForHumans() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-6 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        @if($tool->reviews->count() >= 3)
                            <p class="text-sm font-semibold text-gray-600">Insights ready to be generated</p>
                            @if(Auth::check() && Auth::user()->isAdmin())
                                <p class="text-xs text-gray-400 mt-1">Click "Generate Insights" above to create them with AI.</p>
                            @else
                                <p class="text-xs text-gray-400 mt-1">An administrator will generate the AI analysis soon.</p>
                            @endif
                        @else
                            <p class="text-sm font-semibold text-gray-600">More reviews needed</p>
                            <p class="text-xs text-gray-400 mt-1">At least 3 teacher ratings are required to synthesize information with AI. (Need {{ 3 - $tool->reviews->count() }} more).</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-amber-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <h3 class="text-lg font-heading font-bold text-gray-800">Reviews ({{ $tool->reviews->count() }})</h3>
            </div>

            @auth
            <!-- Review Form -->
            <form method="POST" action="{{ route('reviews.store', $tool) }}" class="mb-6 p-4 bg-gray-50 rounded-xl">
                @csrf
                <p class="text-sm font-semibold text-gray-700 mb-3">{{ $userReview ? 'Update your review' : 'Write a review' }}</p>
                <div class="flex items-center gap-1 mb-3" id="star-rating">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" onclick="setRating({{ $i }})" class="text-2xl transition-colors star-btn {{ $userReview && $i <= $userReview->rating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400">★</button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating-input" value="{{ $userReview ? $userReview->rating : '' }}">
                <textarea name="comment" rows="2" placeholder="Share your experience..." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all resize-none mb-3">{{ $userReview?->comment }}</textarea>
                <button type="submit" class="px-5 py-2 bg-ans-dark-green text-white rounded-xl text-sm font-semibold hover:bg-ans-seal-green transition-all">Submit Review</button>
            </form>
            @endauth

            <!-- Reviews List -->
            @forelse($tool->reviews as $review)
            <div class="p-4 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-3 mb-2">
                    @if($review->user->avatar)
                        <img src="{{ $review->user->avatar }}" alt="" class="w-8 h-8 rounded-full">
                    @else
                        <div class="w-8 h-8 rounded-full bg-ans-dark-green text-white flex items-center justify-center text-xs font-bold">{{ substr($review->user->name, 0, 1) }}</div>
                    @endif
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $review->user->name }}</p>
                        <div class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                            @endfor
                        </div>
                    </div>
                    <span class="ml-auto text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                </div>
                @if($review->comment)
                    <p class="text-sm text-gray-600 ml-11">{{ $review->comment }}</p>
                @endif
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">No reviews yet. Be the first!</p>
            @endforelse
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Stats Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <h4 class="font-heading font-bold text-gray-800">Stats</h4>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Rating</span>
                    <div class="flex items-center gap-1">
                        <span class="text-yellow-400">★</span>
                        <span class="font-bold text-gray-800">{{ number_format($tool->avg_rating, 1) }}</span>
                        <span class="text-xs text-gray-400">({{ $tool->reviews->count() }})</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Views</span>
                    <span class="font-bold text-gray-800">{{ number_format($tool->views_count) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Favorites</span>
                    <span class="font-bold text-gray-800">{{ number_format($tool->favorited_by_count) }}</span>
                </div>
                @if($tool->creator)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Added by</span>
                    <span class="text-sm font-medium text-gray-700">{{ $tool->creator->name }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Related Tools -->
        @if($relatedTools->count())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                <h4 class="font-heading font-bold text-gray-800">Related Tools</h4>
            </div>
            <div class="space-y-3">
                @foreach($relatedTools as $related)
                <a href="{{ route('tools.show', $related) }}" class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors group">
                    @if($related->logo_url)
                        <img src="{{ $related->logo_url }}" alt="" class="w-9 h-9 rounded-lg object-cover border border-gray-200">
                    @else
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-ans-dark-green to-ans-light-green flex items-center justify-center text-white font-bold text-xs">{{ substr($related->name, 0, 2) }}</div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-ans-dark-green transition-colors">{{ $related->name }}</p>
                        <p class="text-xs text-gray-500 flex items-center gap-0.5">
                            <svg class="w-3 h-3 text-amber-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            {{ number_format($related->avg_rating, 1) }}
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function setRating(stars) {
    document.getElementById('rating-input').value = stars;
    document.querySelectorAll('.star-btn').forEach((btn, i) => {
        btn.classList.toggle('text-yellow-400', i < stars);
        btn.classList.toggle('text-gray-300', i >= stars);
    });
}

function toggleFavorite(toolId) {
    fetch(`/tools/${toolId}/favorite`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    })
    .then(r => r.json())
    .then(data => {
        const btn = document.getElementById(`fav-btn-${toolId}`);
        const txt = document.getElementById(`fav-text-${toolId}`);
        const svg = btn.querySelector('svg');
        if (data.favorited) {
            btn.className = btn.className.replace('border-gray-200 bg-white text-gray-600 hover:border-red-300 hover:text-red-500', 'border-red-300 bg-red-50 text-red-500');
            svg.setAttribute('fill', 'currentColor');
            txt.textContent = 'Favorited';
        } else {
            btn.className = btn.className.replace('border-red-300 bg-red-50 text-red-500', 'border-gray-200 bg-white text-gray-600 hover:border-red-300 hover:text-red-500');
            svg.setAttribute('fill', 'none');
            txt.textContent = 'Add to Favorites';
        }
    });
}
</script>

@endsection
