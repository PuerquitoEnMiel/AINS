{{--
    Heart / Favorite Button Partial
    
    Required: $tool (Tool model) — needs $tool->name and $tool->id
    Optional: $toolName, $toolId — override from non-model contexts
--}}
@php
    $heartName = $toolName ?? $tool->name;
    $heartId   = $toolId ?? $tool->id;
@endphp
<button onclick="toggleCardFavorite(event, this, '{{ $heartName }}', {{ $heartId }})" 
        class="card-fav-btn absolute top-4 right-4 z-10 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-gray-400 hover:text-red-500 flex items-center justify-center transition-all shadow-sm border border-gray-100/50 hover:scale-105 active:scale-95" 
        data-tool-name="{{ $heartName }}" title="Favorite">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
</button>
