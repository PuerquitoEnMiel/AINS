@extends('layouts.app')

@section('title', $resource->title . ' — Learning Hub')

@section('content')
<div class="resource-show-page">
    {{-- Breadcrumb --}}
    <nav class="resource-breadcrumb">
        <a href="{{ route('resources.index') }}">🏠 Learning Hub</a>
        <span>›</span>
        <a href="{{ route('resources.index', ['area' => $resource->area]) }}">{{ $resource->areaLabel() }}</a>
        <span>›</span>
        <span>{{ Str::limit($resource->title, 40) }}</span>
    </nav>

    <div class="resource-show-grid">
        {{-- ── Main content ─────────────────────────────────────────── --}}
        <div class="resource-show-main">
            {{-- Header card --}}
            <div class="resource-show-header">
                {{-- Thumbnail --}}
                @if($resource->thumbnail_url)
                    <img src="{{ $resource->thumbnail_url }}"
                         alt="{{ $resource->title }}"
                         class="resource-show-thumb"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="resource-show-thumb-placeholder area-{{ $resource->area }}" style="display:none;">
                        {{ $resource->typeIcon() }}
                    </div>
                @else
                    <div class="resource-show-thumb-placeholder area-{{ $resource->area }}">
                        {{ $resource->typeIcon() }}
                    </div>
                @endif

                <div class="resource-show-header__body">
                    {{-- Badges --}}
                    <div class="resource-card__badges" style="margin-bottom:14px;">
                        <span class="resource-badge resource-badge--{{ $resource->area }}">{{ $resource->areaLabel() }}</span>
                        <span class="resource-badge resource-badge--type">{{ $resource->typeIcon() }} {{ ucfirst($resource->type) }}</span>
                    </div>

                    <h1 class="resource-show-title">{{ $resource->title }}</h1>

                    @if($resource->author || $resource->source)
                        <p class="resource-show-meta">
                            @if($resource->author)<span>✍️ {{ $resource->author }}</span>@endif
                            @if($resource->source)<span>🏛️ {{ $resource->source }}</span>@endif
                        </p>
                    @endif

                    @if($resource->description)
                        <p class="resource-show-desc">{{ $resource->description }}</p>
                    @endif

                    {{-- CTAs --}}
                    <div class="resource-show-cta">
                        @if($resource->url)
                            <a href="{{ $resource->url }}" target="_blank" rel="noopener" class="resource-show-btn resource-show-btn--primary">
                                🔗 Open Resource →
                            </a>
                        @endif

                        @auth
                        <form method="POST" action="{{ route('resources.save', $resource) }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="resource-show-btn {{ $isSaved ? 'resource-show-btn--saved' : 'resource-show-btn--secondary' }}">
                                {{ $isSaved ? '♥ Saved' : '♡ Save Resource' }}
                            </button>
                        </form>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- Embed video if YouTube --}}
            @if($resource->url && str_contains($resource->url, 'youtube'))
                @php
                    preg_match('/(?:v=|youtu\.be\/)([^&]+)/', $resource->url, $m);
                    $videoId = $m[1] ?? null;
                @endphp
                @if($videoId)
                <div class="resource-video-wrap">
                    <iframe
                        src="https://www.youtube.com/embed/{{ $videoId }}"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        class="resource-video">
                    </iframe>
                </div>
                @endif
            @endif
        </div>

        {{-- ── Sidebar: Related ─────────────────────────────────────── --}}
        <aside class="resource-show-aside">
            <h2 class="resource-aside-title">More in {{ $resource->areaLabel() }}</h2>
            @forelse($related as $rel)
                <a href="{{ route('resources.show', $rel) }}" class="resource-aside-card">
                    <div class="resource-aside-card__icon area-{{ $rel->area }}">{{ $rel->typeIcon() }}</div>
                    <div>
                        <p class="resource-aside-card__title">{{ Str::limit($rel->title, 55) }}</p>
                        <p class="resource-aside-card__source">{{ $rel->source }}</p>
                    </div>
                </a>
            @empty
                <p class="resource-aside-empty">No other resources in this area yet.</p>
            @endforelse

            {{-- Back button --}}
            <a href="{{ route('resources.index') }}" class="resource-aside-back">
                ← Back to Learning Hub
            </a>
        </aside>
    </div>
</div>

<style>
.resource-show-page { max-width: 1200px; margin: 0 auto; padding: 32px 24px 80px; }

/* Breadcrumb */
.resource-breadcrumb {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px; color: #4b5563; margin-bottom: 28px;
    flex-wrap: wrap;
}
.resource-breadcrumb a { color: #1f2937; font-weight: 500; text-decoration: none; transition: color 0.2s; }
.resource-breadcrumb a:hover { color: #FF8300; }
.resource-breadcrumb span { color: #4b5563; }

/* Grid */
.resource-show-grid { display: grid; grid-template-columns: 1fr 300px; gap: 28px; align-items: start; }
@media (max-width: 900px) { .resource-show-grid { grid-template-columns: 1fr; } }

/* Header card */
.resource-show-header {
    background: #ffffff; border: 1px solid #e5e7eb;
    border-radius: 16px; overflow: hidden; margin-bottom: 24px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
}
.resource-show-thumb { width: 100%; max-height: 280px; object-fit: cover; }
.resource-show-thumb-placeholder {
    width: 100%; height: 200px;
    display: flex; align-items: center; justify-content: center; font-size: 64px;
    color: #ffffff;
}
.resource-show-thumb-placeholder.area-stem        { background: linear-gradient(135deg, #003a1c, #005f2e); }
.resource-show-thumb-placeholder.area-innovation  { background: linear-gradient(135deg, #7a3c00, #b55a00); }
.resource-show-thumb-placeholder.area-ai          { background: linear-gradient(135deg, #1e1b6a, #3730a3); }
.resource-show-thumb-placeholder.area-robotics    { background: linear-gradient(135deg, #0c3a5a, #0369a1); }
.resource-show-thumb-placeholder.area-design      { background: linear-gradient(135deg, #6b0033, #9d174d); }
.resource-show-thumb-placeholder.area-programming { background: linear-gradient(135deg, #3b0764, #6d28d9); }
.resource-show-thumb-placeholder.area-math        { background: linear-gradient(135deg, #78350f, #b45309); }
.resource-show-thumb-placeholder.area-science     { background: linear-gradient(135deg, #042f2e, #0f766e); }
.resource-show-thumb-placeholder.area-general     { background: linear-gradient(135deg, #1f2937, #374151); }

.resource-show-header__body { padding: 24px; }
.resource-show-title {
    font-family: 'Montserrat', sans-serif;
    font-size: clamp(20px, 3vw, 28px); font-weight: 700;
    color: #111827; line-height: 1.3; margin-bottom: 10px;
}
.resource-show-meta {
    display: flex; flex-wrap: wrap; gap: 16px;
    font-size: 13px; color: #6b7280; margin-bottom: 14px;
}
.resource-show-desc {
    font-size: 15px; color: #374151;
    line-height: 1.6; margin-bottom: 20px;
}
.resource-show-cta { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
.resource-show-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 22px; border-radius: 10px;
    font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer;
    border: none; transition: all 0.2s;
}
.resource-show-btn--primary {
    background: linear-gradient(135deg, #007934, #00a04a);
    color: #fff; box-shadow: 0 4px 15px rgba(0,121,52,0.15);
}
.resource-show-btn--primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,121,52,0.25); }
.resource-show-btn--secondary {
    background: #f3f4f6; border: 1px solid #e5e7eb; color: #4b5563;
}
.resource-show-btn--secondary:hover { background: #e5e7eb; color: #111827; }
.resource-show-btn--saved { background: rgba(255,131,0,0.15); border: 1px solid #FF8300; color: #FF8300; }

/* Video embed */
.resource-video-wrap {
    background: #000; border-radius: 12px; overflow: hidden;
    aspect-ratio: 16/9; margin-bottom: 24px;
}
.resource-video { width: 100%; height: 100%; border: none; }

/* Aside */
.resource-show-aside {
    background: #ffffff; border: 1px solid #e5e7eb;
    border-radius: 16px; padding: 20px;
    position: sticky; top: 80px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
}
.resource-aside-title {
    font-size: 14px; font-weight: 700; color: #4b5563;
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 14px;
}
.resource-aside-card {
    display: flex; gap: 12px; padding: 10px; border-radius: 10px;
    text-decoration: none; transition: background 0.2s; margin-bottom: 4px;
}
.resource-aside-card:hover { background: #f9fafb; }
.resource-aside-card__icon {
    width: 38px; height: 38px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.resource-aside-card__title { font-size: 13px; color: #111827; line-height: 1.3; }
.resource-aside-card__source { font-size: 11px; color: #6b7280; margin-top: 3px; }
.resource-aside-empty { font-size: 13px; color: #9ca3af; }
.resource-aside-back {
    display: block; margin-top: 20px; padding-top: 14px;
    border-top: 1px solid #e5e7eb;
    font-size: 13px; color: #4b5563; text-decoration: none; transition: color 0.2s;
}
.resource-aside-back:hover { color: #FF8300; }
</style>
@endsection
