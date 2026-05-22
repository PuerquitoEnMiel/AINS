@php
    $userId = Auth::id();
    $hasUpvoted = $prompt->hasVotedBy($userId, 'upvote');
    $hasDownvoted = $prompt->hasVotedBy($userId, 'downvote');

    // Mapeo de categorías y complejidad a Inglés
    $catMap = [
        'Evaluación' => 'Evaluation',
        'Planificación' => 'Planning',
        'Administración' => 'Administration',
        'Retroalimentación' => 'Feedback',
        'Investigación' => 'Research',
        'Diseño de Actividades' => 'Activity Design',
        'Desarrollo Profesional' => 'Professional Development'
    ];
    $displayCategory = isset($catMap[$prompt->category]) ? $catMap[$prompt->category] : $prompt->category;

    $complexityMap = [
        'Básico' => 'Basic',
        'Intermedio' => 'Intermediate',
        'Avanzado' => 'Advanced'
    ];
    $displayComplexity = isset($complexityMap[$prompt->complexity]) ? $complexityMap[$prompt->complexity] : $prompt->complexity;
@endphp

<div class="prompt-card bg-white rounded-3xl p-6 flex flex-col justify-between shadow-sm relative overflow-hidden {{ !$prompt->is_approved ? 'border-red-200 bg-red-50/10' : '' }}">
    
    @if(!$prompt->is_approved)
        <div class="absolute top-0 inset-x-0 bg-red-500 text-white text-[10px] font-bold text-center py-1 uppercase tracking-wider">
            Pending Approval
        </div>
    @endif

    <div class="space-y-4 {{ !$prompt->is_approved ? 'mt-4' : '' }}">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-bold bg-ans-dark-green/10 text-ans-dark-green px-2.5 py-1 rounded-full uppercase tracking-wider">{{ $displayCategory }}</span>
            <span class="text-xs text-gray-400">Complexity: {{ $displayComplexity }}</span>
        </div>

        <div>
            <h5 class="font-bold text-gray-900 text-base flex items-center gap-1.5">
                {{ $prompt->title }}
                @if($prompt->is_community)
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded text-[9px] font-bold">Community</span>
                @endif
            </h5>
            
            @if($prompt->is_community && $prompt->author)
                <p class="text-[10px] text-gray-400 mt-1">By: <span class="font-semibold text-gray-500">{{ $prompt->author->name }}</span></p>
            @endif
        </div>

        <p class="text-xs text-gray-600 leading-relaxed font-sans">
            {{ $prompt->description }}
        </p>

        <!-- Prompt Preview Box -->
        <div class="bg-gray-50 border border-gray-100 rounded-xl p-3.5 text-[11px] text-gray-500 font-mono select-none h-24 overflow-y-auto leading-normal">
            {{ $prompt->prompt_text }}
        </div>
    </div>

    <!-- Actions & Votes -->
    <div class="mt-6 space-y-3">
        <div class="flex items-center justify-between flex-wrap gap-2 pt-3 border-t border-gray-50">
            <!-- Votes Component -->
            <div class="flex items-center gap-1 bg-gray-100/80 p-1 rounded-xl">
                <button onclick="handleVote({{ $prompt->id }}, 'upvote')" id="upvote-btn-{{ $prompt->id }}" class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold transition-all text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 {{ $hasUpvoted ? 'text-emerald-600 bg-emerald-50' : '' }}">▲</button>
                <span id="score-{{ $prompt->id }}" class="text-xs font-bold text-gray-700 px-1">{{ $prompt->voteCount() }}</span>
                <button onclick="handleVote({{ $prompt->id }}, 'downvote')" id="downvote-btn-{{ $prompt->id }}" class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold transition-all text-gray-500 hover:text-red-600 hover:bg-red-50 {{ $hasDownvoted ? 'text-red-600 bg-red-50' : '' }}">▼</button>
            </div>

            <!-- Stats/Actions -->
            <div class="flex items-center gap-2">
                <button onclick="toggleComments({{ $prompt->id }})" id="comment-count-badge-{{ $prompt->id }}" class="text-[10px] text-gray-500 font-bold hover:underline">
                    Comments ({{ $prompt->comments->count() }})
                </button>
                <span class="text-gray-300 text-xs">•</span>
                <span id="copy-count-{{ $prompt->id }}" class="text-[10px] text-gray-400 font-medium">
                    {{ $prompt->usage_count }} copies
                </span>
            </div>
        </div>

        <div class="flex gap-2">
            @if(Auth::check() && Auth::user()->isAdmin() && !$prompt->is_approved)
                <form action="{{ route('admin.prompt-tips.toggleApproval', $prompt) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                        Approve Prompt
                    </button>
                </form>
            @endif

            <button data-prompt-text="{{ $prompt->prompt_text }}" onclick="copyPrompt(this, {{ $prompt->id }})" class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2 bg-ans-dark-green hover:bg-ans-seal-green text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                <span>Copy Prompt</span>
            </button>
        </div>
    </div>

    <!-- Comments Section (Collapsed by default) -->
    <div id="comments-{{ $prompt->id }}" class="hidden mt-4 pt-4 border-t border-gray-100 space-y-3">
        <h6 class="text-xs font-bold text-gray-700 mb-2">Comments</h6>

        <!-- Comment Input -->
        @auth
            <form onsubmit="submitComment(event, {{ $prompt->id }})" class="flex gap-2">
                <input type="text" id="comment-input-{{ $prompt->id }}" required placeholder="Write a comment..." class="flex-1 px-3 py-1.5 rounded-lg border border-gray-200 focus:outline-none focus:ring-1 focus:ring-ans-dark-green text-xs">
                <button type="submit" class="px-3 py-1.5 bg-ans-dark-green text-white text-xs font-semibold rounded-lg hover:bg-ans-seal-green transition-all">Send</button>
            </form>
        @else
            <p class="text-[10px] text-gray-400 text-center py-1">Log in to leave a comment.</p>
        @endauth

        <!-- Comments List -->
        <div id="comment-list-{{ $prompt->id }}" class="space-y-2.5 max-h-48 overflow-y-auto pr-1">
            @forelse($prompt->comments as $comment)
                <div class="p-2.5 bg-gray-50 rounded-xl flex items-start gap-2.5 border border-gray-100/50">
                    <div class="w-6.5 h-6.5 rounded-full bg-ans-dark-green text-white flex items-center justify-center text-[10px] font-bold">
                        {{ substr($comment->user->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] font-bold text-gray-800">{{ $comment->user->name }}</p>
                            <span class="text-[8px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[10px] text-gray-600 mt-0.5">{{ $comment->body }}</p>
                    </div>
                </div>
            @empty
                <p class="text-[10px] text-gray-400 text-center py-2" id="no-comments-{{ $prompt->id }}">No comments yet.</p>
            @endforelse
        </div>
    </div>

</div>
