{{-- resources/views/resources/_card.blade.php --}}
@php
    $isSaved = in_array($resource->id, $savedIds ?? []);
    $showStatus = $showStatus ?? false;
@endphp

<div class="resource-card">
    {{-- Thumbnail --}}
    @if($resource->thumbnail_url)
        <img src="{{ $resource->thumbnail_url }}"
             alt="{{ $resource->title }}"
             class="resource-card__thumbnail"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="resource-card__thumbnail-placeholder area-{{ $resource->area }}" style="display:none;">
            {{ $resource->typeIcon() }}
        </div>
    @else
        <div class="resource-card__thumbnail-placeholder area-{{ $resource->area }}">
            {{ $resource->typeIcon() }}
        </div>
    @endif

    <div class="resource-card__body">
        {{-- Badges row --}}
        <div class="resource-card__badges">
            <span class="resource-badge resource-badge--{{ $resource->area }}">
                {{ $resource->areaLabel() }}
            </span>
            <span class="resource-badge resource-badge--type">
                {{ $resource->typeIcon() }} {{ ucfirst($resource->type) }}
            </span>
            @if($showStatus)
                @if($resource->is_published)
                    <span class="resource-badge resource-badge--published">✓ Published</span>
                @else
                    <span class="resource-badge resource-badge--pending">⏳ Pending</span>
                @endif
            @endif
        </div>

        {{-- Title --}}
        <a href="{{ route('resources.show', $resource) }}" class="resource-card__title">
            {{ $resource->title }}
        </a>

        {{-- Source --}}
        @if($resource->source || $resource->author)
            <p class="resource-card__source">
                @if($resource->author) {{ $resource->author }} @endif
                @if($resource->author && $resource->source) · @endif
                @if($resource->source) {{ $resource->source }} @endif
            </p>
        @endif

        {{-- Description --}}
        @if($resource->description)
            <p class="resource-card__desc">{{ $resource->description }}</p>
        @endif

        {{-- Footer --}}
        <div class="resource-card__footer">
            <a href="{{ $resource->url ?? route('resources.show', $resource) }}"
               @if($resource->url) target="_blank" rel="noopener" @endif
               class="resource-card__link">
                @if($resource->url) 🔗 Open Resource @else 📄 View Details @endif →
            </a>

            @auth
            <form method="POST" action="{{ route('resources.save', $resource) }}" style="margin:0;">
                @csrf
                <button type="submit"
                        class="resource-card__save-btn {{ $isSaved ? 'resource-card__save-btn--saved' : '' }}"
                        title="{{ $isSaved ? 'Remove from saved' : 'Save resource' }}">
                    {{ $isSaved ? '♥' : '♡' }}
                </button>
            </form>
            @endauth
        </div>
    </div>
</div>
