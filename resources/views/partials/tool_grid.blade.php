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
     data-tool='@include("partials._tool_data", ["tool" => $tool])'
     onclick="openToolModal(JSON.parse(this.dataset.tool))">
    
    <!-- Heart Button -->
    @include('partials._tool_heart', ['tool' => $tool])

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
