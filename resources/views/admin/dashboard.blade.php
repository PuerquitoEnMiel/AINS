@extends('layouts.app')

@section('header-title', 'Admin Dashboard')
@section('header-subtitle', 'Platform analytics, impact metrics, and management overview')

@section('content')

<!-- ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<!-- ── Summary Stats Cards (Row 1) ───────────────────────────── -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4 mb-8">
    @php
        $statCards = [
            [
                'label' => 'Total Users', 
                'value' => $stats['total_users'], 
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>', 
                'color' => 'from-emerald-500 to-green-600'
            ],
            [
                'label' => 'Approved Tools', 
                'value' => $stats['total_tools'], 
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>', 
                'color' => 'from-blue-500 to-indigo-600'
            ],
            [
                'label' => 'Pending Requests', 
                'value' => $stats['pending_requests'], 
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>', 
                'color' => 'from-amber-500 to-orange-600'
            ],
            [
                'label' => 'Conversations', 
                'value' => $stats['total_chats'], 
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>', 
                'color' => 'from-purple-500 to-violet-600'
            ],
            [
                'label' => 'Reviews', 
                'value' => $stats['total_reviews'], 
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>', 
                'color' => 'from-yellow-500 to-amber-600'
            ],
            [
                'label' => 'Tool Views', 
                'value' => $stats['total_views'], 
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>', 
                'color' => 'from-rose-500 to-pink-600'
            ],
            [
                'label' => 'Lesson Plans', 
                'value' => $stats['lesson_plans'], 
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>', 
                'color' => 'from-cyan-500 to-teal-600'
            ],
            [
                'label' => 'Prompt Tips', 
                'value' => $stats['prompt_tips'], 
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 01-2 2h0a2 2 0 01-2-2v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>', 
                'color' => 'from-fuchsia-500 to-purple-600'
            ],
        ];
    @endphp
    @foreach($statCards as $card)
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-all group flex flex-col justify-between">
        <div>
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $card['color'] }} flex items-center justify-center text-white shadow-sm shadow-black/10 group-hover:scale-110 transition-transform duration-300">
                {!! $card['icon'] !!}
            </div>
            <p class="text-2xl font-heading font-extrabold text-gray-900 mt-4 animate-stat-count" data-target="{{ $card['value'] }}">{{ number_format($card['value']) }}</p>
        </div>
        <p class="text-xs text-gray-500 mt-1.5 font-medium leading-none">{{ $card['label'] }}</p>
    </div>
    @endforeach
</div>

<!-- ── Charts Row 1: Activity + Roles ──────────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Activity Area Chart (2 cols) -->
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
            Tool Views — Last 30 Days
        </h3>
        <div id="activity-chart"></div>
    </div>

    <!-- Role Distribution Donut -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            User Roles
        </h3>
        <div id="roles-chart"></div>
        <div class="mt-4 text-center">
            <span class="text-xs text-gray-500">{{ $newUsersThisWeek }} new users this week</span>
        </div>
    </div>
</div>

<!-- ── Charts Row 2: Adoption Trend + Weekly Heatmap ──────────── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Monthly Adoption Trend -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            Monthly Adoption Trend
        </h3>
        <p class="text-xs text-gray-500 mb-4">New favorites added per month (last 6 months)</p>
        <div id="adoption-chart"></div>
    </div>

    <!-- Weekly Activity Heatmap -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Weekly Activity Heatmap
        </h3>
        <p class="text-xs text-gray-500 mb-4">Tool views by day of week (last 30 days)</p>
        <div id="heatmap-chart"></div>
        <div class="mt-4 grid grid-cols-2 gap-3">
            <div class="bg-gray-50 rounded-xl p-3 text-center">
                <p class="text-lg font-extrabold text-gray-800">{{ $lessonPlansThisWeek }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider">Lesson Plans This Week</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 text-center">
                <p class="text-lg font-extrabold text-gray-800">{{ number_format($avgReviewsPerTool ?? 0, 1) }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider">Avg Reviews / Tool</p>
            </div>
        </div>
    </div>
</div>

<!-- ── Charts Row 3: Category Distribution + Catalog Gaps ────── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Category Distribution Bar -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Tools by Category
        </h3>
        <div id="category-chart"></div>
    </div>

    <!-- Catalog Gaps Analysis -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            Catalog Gaps Analysis
        </h3>
        <p class="text-xs text-gray-500 mb-4">Categories with fewer than 3 approved tools need attention</p>
        <div class="space-y-2.5 max-h-72 overflow-y-auto pr-2">
            @foreach($catalogGaps->sortBy('count') as $gap)
            <div class="flex items-center gap-3 p-3 rounded-xl {{ $gap['status'] === 'empty' ? 'bg-red-50 border border-red-100' : ($gap['status'] === 'low' ? 'bg-amber-50 border border-amber-100' : 'bg-green-50 border border-green-100') }}">
                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $gap['name'] }}</p>
                    <div class="w-full bg-gray-200/50 rounded-full h-1.5 mt-1">
                        <div class="h-1.5 rounded-full {{ $gap['status'] === 'empty' ? 'bg-red-400' : ($gap['status'] === 'low' ? 'bg-amber-400' : 'bg-green-400') }}" style="width: {{ min(100, ($gap['count'] / max($catalogGaps->max('count'), 1)) * 100) }}%"></div>
                    </div>
                </div>
                <span class="text-sm font-bold {{ $gap['status'] === 'empty' ? 'text-red-600' : ($gap['status'] === 'low' ? 'text-amber-600' : 'text-green-600') }}">{{ $gap['count'] }}</span>
                @if($gap['status'] === 'empty')
                    <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-lg text-[10px] font-bold uppercase">Empty</span>
                @elseif($gap['status'] === 'low')
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-lg text-[10px] font-bold uppercase">Low</span>
                @else
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-lg text-[10px] font-bold uppercase">OK</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ── Row 4: Top Tools (Clicks) vs Top Favorited ────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Top 5 by Clicks -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Top 5 by Clicks
        </h3>
        <div class="space-y-3">
            @forelse($topTools as $i => $tool)
            <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors">
                <span class="text-lg font-bold text-gray-300 w-6 text-center">#{{ $i + 1 }}</span>
                @if($tool->logo_url)
                    <img src="{{ $tool->logo_url }}" alt="" class="w-10 h-10 rounded-xl object-cover border border-gray-200">
                @else
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-ans-dark-green to-ans-light-green flex items-center justify-center text-white font-bold text-sm">{{ substr($tool->name, 0, 2) }}</div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $tool->name }}</p>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <span>{{ number_format($tool->click_count) }} clicks</span>
                        <span>•</span>
                        <span>⭐ {{ number_format($tool->avg_rating, 1) }}</span>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-8">No tools data yet.</p>
            @endforelse
        </div>
    </div>

    <!-- Top 10 by Favorites -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
            Top 10 Most Favorited
        </h3>
        <div class="space-y-2.5 max-h-80 overflow-y-auto pr-2">
            @forelse($topFavorited as $i => $tool)
            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors">
                <span class="text-sm font-bold text-gray-300 w-5 text-center">{{ $i + 1 }}</span>
                @if($tool->logo_url)
                    <img src="{{ $tool->logo_url }}" alt="" class="w-8 h-8 rounded-lg object-cover border border-gray-200">
                @else
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-ans-dark-green to-ans-light-green flex items-center justify-center text-white font-bold text-[10px]">{{ substr($tool->name, 0, 2) }}</div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-xs truncate">{{ $tool->name }}</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-red-500 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    <span class="text-xs font-bold text-gray-700">{{ $tool->favorited_by_count }}</span>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-8">No favorites data yet.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- ── Pending Requests ──────────────────────────────────────── -->
@if($recentRequests->count())
<div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-heading font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            Pending Requests
        </h3>
        <a href="{{ route('admin.requests.index') }}" class="text-sm text-ans-dark-green font-semibold hover:underline">View All →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 uppercase tracking-wider border-b border-gray-100">
                    <th class="pb-3 pr-4">Tool Name</th>
                    <th class="pb-3 pr-4">Requester</th>
                    <th class="pb-3 pr-4">Category</th>
                    <th class="pb-3">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentRequests as $req)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 pr-4 font-semibold text-gray-800">{{ $req->tool_name }}</td>
                    <td class="py-3 pr-4 text-gray-600">{{ $req->requester_name }}</td>
                    <td class="py-3 pr-4"><span class="px-2 py-1 bg-gray-100 rounded-lg text-xs">{{ $req->category }}</span></td>
                    <td class="py-3 text-gray-500">{{ $req->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- ── ApexCharts Initialization ─────────────────────────────── -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartDefaults = {
        fontFamily: 'Inter, sans-serif',
        toolbar: { show: false },
    };

    // Activity Area Chart
    const activityData = @json($activityChart);
    new ApexCharts(document.querySelector('#activity-chart'), {
        chart: { type: 'area', height: 280, ...chartDefaults },
        series: [{ name: 'Views', data: Object.values(activityData) }],
        xaxis: {
            categories: Object.keys(activityData).map(d => {
                const date = new Date(d + 'T00:00:00');
                return date.toLocaleDateString('en', { month: 'short', day: 'numeric' });
            }),
            labels: { style: { fontSize: '10px', colors: '#9CA3AF' } },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: { labels: { style: { fontSize: '10px', colors: '#9CA3AF' } } },
        colors: ['#007934'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
        stroke: { curve: 'smooth', width: 2 },
        grid: { borderColor: '#F3F4F6', strokeDashArray: 4 },
        dataLabels: { enabled: false },
        tooltip: { theme: 'light' },
    }).render();

    // Roles Donut Chart
    const roleData = @json($roleBreakdown);
    new ApexCharts(document.querySelector('#roles-chart'), {
        chart: { type: 'donut', height: 220, fontFamily: 'Inter, sans-serif' },
        series: Object.values(roleData),
        labels: Object.keys(roleData).map(r => r.charAt(0).toUpperCase() + r.slice(1)),
        colors: ['#007934', '#FF8300', '#80BC00'],
        legend: { position: 'bottom', fontSize: '11px' },
        plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '12px', fontWeight: 700 } } } } },
        dataLabels: { enabled: false },
    }).render();

    // Monthly Adoption Trend (Line)
    const adoptionData = @json($adoptionChart);
    new ApexCharts(document.querySelector('#adoption-chart'), {
        chart: { type: 'line', height: 240, ...chartDefaults },
        series: [{ name: 'New Favorites', data: Object.values(adoptionData) }],
        xaxis: {
            categories: Object.keys(adoptionData).map(m => {
                const [y, mo] = m.split('-');
                const date = new Date(y, mo - 1);
                return date.toLocaleDateString('en', { month: 'short', year: '2-digit' });
            }),
            labels: { style: { fontSize: '10px', colors: '#9CA3AF' } },
            axisBorder: { show: false },
        },
        yaxis: { labels: { style: { fontSize: '10px', colors: '#9CA3AF' } } },
        colors: ['#FF8300'],
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 5, colors: ['#FF8300'], strokeWidth: 2, strokeColors: '#fff' },
        grid: { borderColor: '#F3F4F6', strokeDashArray: 4 },
        dataLabels: { enabled: false },
    }).render();

    // Weekly Heatmap (Bar)
    const heatmapData = @json($heatmapData);
    const maxViews = Math.max(...Object.values(heatmapData), 1);
    new ApexCharts(document.querySelector('#heatmap-chart'), {
        chart: { type: 'bar', height: 200, ...chartDefaults },
        series: [{ name: 'Views', data: Object.values(heatmapData) }],
        xaxis: {
            categories: Object.keys(heatmapData),
            labels: { style: { fontSize: '11px', colors: '#6B7280', fontWeight: 600 } },
            axisBorder: { show: false },
        },
        yaxis: { labels: { style: { fontSize: '10px', colors: '#9CA3AF' } } },
        colors: Object.values(heatmapData).map(v => {
            const intensity = v / maxViews;
            if (intensity > 0.7) return '#007934';
            if (intensity > 0.4) return '#80BC00';
            if (intensity > 0.1) return '#FFD580';
            return '#F3F4F6';
        }),
        plotOptions: {
            bar: { borderRadius: 6, columnWidth: '60%', distributed: true },
        },
        legend: { show: false },
        grid: { borderColor: '#F3F4F6', strokeDashArray: 4 },
        dataLabels: { enabled: true, style: { fontSize: '10px', fontWeight: 700 } },
    }).render();

    // Category Bar Chart
    const catData = @json($categoryDistribution);
    new ApexCharts(document.querySelector('#category-chart'), {
        chart: { type: 'bar', height: 260, ...chartDefaults },
        series: [{ name: 'Tools', data: catData.map(c => c.total) }],
        xaxis: {
            categories: catData.map(c => c.name),
            labels: { style: { fontSize: '10px', colors: '#9CA3AF' }, rotate: -45 },
            axisBorder: { show: false },
        },
        yaxis: { labels: { style: { fontSize: '10px', colors: '#9CA3AF' } } },
        colors: ['#FF8300'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
        grid: { borderColor: '#F3F4F6', strokeDashArray: 4 },
        dataLabels: { enabled: false },
    }).render();
});
</script>

@endsection
