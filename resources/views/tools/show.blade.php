@extends('layouts.app')

@section('header-title', $tool->name)
@section('header-subtitle', $tool->categoryRelation ? $tool->categoryRelation->icon . ' ' . $tool->categoryRelation->name : 'Tool Details')

@section('content')

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
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium">🔷 Google Workspace</span>
                            @endif
                            @if($tool->featured)
                                <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-xs font-medium">⭐ Featured</span>
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

        <!-- Reviews Section -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-lg font-heading font-bold text-gray-800 mb-4">⭐ Reviews ({{ $tool->reviews->count() }})</h3>

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
            <h4 class="font-heading font-bold text-gray-800 mb-4">📊 Stats</h4>
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
            <h4 class="font-heading font-bold text-gray-800 mb-4">🔗 Related Tools</h4>
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
                        <p class="text-xs text-gray-500">⭐ {{ number_format($related->avg_rating, 1) }}</p>
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
