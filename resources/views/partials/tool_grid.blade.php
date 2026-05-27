@php
    // Vibrant Tailwind Gradients for placeholders
    $vibrantGradients = [
        'from-purple-500 to-indigo-600',
        'from-pink-500 to-rose-600',
        'from-emerald-500 to-teal-600',
        'from-blue-500 to-indigo-600',
        'from-amber-500 to-orange-600',
        'from-cyan-500 to-blue-600',
        'from-violet-500 to-fuchsia-600',
        'from-red-500 to-ans-orange'
    ];

    $getGradientForName = function($name) use ($vibrantGradients) {
        $hash = 0;
        foreach (str_split($name) as $char) {
            $hash += ord($char);
        }
        return $vibrantGradients[$hash % count($vibrantGradients)];
    };
@endphp

@foreach((is_iterable($tools) ? $tools : []) as $index => $tool)
@if(!is_object($tool) || !empty($tool->is_official))
    @continue
@endif
<div class="group premium-card bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-xl hover:shadow-gray-100 hover:-translate-y-1 transition-all duration-300 cursor-pointer relative overflow-visible animate-fade-in-up"
     style="animation-delay: {{ 0.05 * ($index + 1) }}s;"
     data-tool='{!! json_encode(['id'=>$tool->id,'name'=>$tool->name,'desc'=>$tool->description,'url'=>$tool->url,'cat'=>$tool->categoryRelation?->name, 'type'=>$tool->is_google_workspace?'Google Workspace':'3rd Party','logo'=>$tool->logo_url?asset($tool->logo_url):null,'compatibility'=>$tool->compatibility], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}'
     onclick="openToolModal(JSON.parse(this.dataset.tool))">
    
    <!-- Heart Button -->
    <button onclick="toggleCardFavorite(event, this, '{{ $tool->name }}', {{ $tool->id }})" class="card-fav-btn absolute top-4 right-4 z-10 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-gray-400 hover:text-red-500 flex items-center justify-center transition-all shadow-sm border border-gray-100/50 hover:scale-105 active:scale-95" data-tool-name="{{ $tool->name }}" title="Favorite">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
    </button>

    <div class="flex items-start justify-between mb-4">
        @if($tool->logo_url)
            <img src="{{ asset($tool->logo_url) }}" alt="{{ $tool->name }}" class="w-12 h-12 rounded-xl object-cover border border-gray-100 group-hover:scale-110 transition-transform duration-300">
        @else
            @php
                $grad = $getGradientForName($tool->name);
            @endphp
            <div class="w-12 h-12 bg-gradient-to-br {{ $grad }} rounded-xl flex items-center justify-center text-white font-bold text-xl group-hover:scale-110 transition-all duration-300 shadow-md shadow-black/10">
                {{ substr($tool->name, 0, 1) }}
            </div>
        @endif
        <div class="w-5 h-5"></div>
    </div>
    <h4 class="font-heading font-bold text-gray-900 text-lg leading-tight">{{ $tool->name }}</h4>
    <p class="text-sm text-gray-500 mt-2 leading-relaxed line-clamp-2">{{ $tool->description }}</p>
    <div class="mt-5 flex items-center gap-2">
        @if($tool->is_google_workspace)
            <span class="text-[10px] font-bold bg-ans-blue/10 text-ans-blue px-2.5 py-1 rounded-full uppercase tracking-wider">Workspace</span>
        @else
            <span class="text-[10px] font-bold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full uppercase tracking-wider">3rd Party</span>
        @endif
        <span class="text-[10px] text-gray-300 ml-auto">Click for details</span>
    </div>
</div>
@endforeach
