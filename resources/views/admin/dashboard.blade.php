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
            ['label' => 'Total Users', 'value' => $stats['total_users'], 'icon' => '👥', 'color' => 'from-emerald-500 to-green-600'],
            ['label' => 'Approved Tools', 'value' => $stats['total_tools'], 'icon' => '🔧', 'color' => 'from-blue-500 to-indigo-600'],
            ['label' => 'Pending Requests', 'value' => $stats['pending_requests'], 'icon' => '📋', 'color' => 'from-amber-500 to-orange-600'],
            ['label' => 'Conversations', 'value' => $stats['total_chats'], 'icon' => '💬', 'color' => 'from-purple-500 to-violet-600'],
            ['label' => 'Reviews', 'value' => $stats['total_reviews'], 'icon' => '⭐', 'color' => 'from-yellow-500 to-amber-600'],
            ['label' => 'Tool Views', 'value' => $stats['total_views'], 'icon' => '👁️', 'color' => 'from-rose-500 to-pink-600'],
            ['label' => 'Lesson Plans', 'value' => $stats['lesson_plans'], 'icon' => '📝', 'color' => 'from-cyan-500 to-teal-600'],
            ['label' => 'Prompt Tips', 'value' => $stats['prompt_tips'], 'icon' => '💡', 'color' => 'from-fuchsia-500 to-purple-600'],
        ];
    @endphp
    @foreach($statCards as $card)
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-all group">
        <div class="flex items-center justify-between mb-3">
            <span class="text-2xl">{{ $card['icon'] }}</span>
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br {{ $card['color'] }} opacity-10 group-hover:opacity-20 transition-opacity"></div>
        </div>
        <p class="text-2xl font-heading font-extrabold text-gray-900">{{ number_format($card['value']) }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $card['label'] }}</p>
    </div>
    @endforeach
</div>

<!-- ── Charts Row 1: Activity + Roles ──────────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Activity Area Chart (2 cols) -->
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4">📊 Tool Views — Last 30 Days</h3>
        <div id="activity-chart"></div>
    </div>

    <!-- Role Distribution Donut -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4">👥 User Roles</h3>
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
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4">📈 Monthly Adoption Trend</h3>
        <p class="text-xs text-gray-500 mb-4">New favorites added per month (last 6 months)</p>
        <div id="adoption-chart"></div>
    </div>

    <!-- Weekly Activity Heatmap -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4">🗓️ Weekly Activity Heatmap</h3>
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
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4">🏷️ Tools by Category</h3>
        <div id="category-chart"></div>
    </div>

    <!-- Catalog Gaps Analysis -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4">⚠️ Catalog Gaps Analysis</h3>
        <p class="text-xs text-gray-500 mb-4">Categories with fewer than 3 approved tools need attention</p>
        <div class="space-y-2.5 max-h-72 overflow-y-auto pr-2">
            @foreach($catalogGaps->sortBy('count') as $gap)
            <div class="flex items-center gap-3 p-3 rounded-xl {{ $gap['status'] === 'empty' ? 'bg-red-50 border border-red-100' : ($gap['status'] === 'low' ? 'bg-amber-50 border border-amber-100' : 'bg-green-50 border border-green-100') }}">
                <span class="text-lg">{{ $gap['icon'] }}</span>
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
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4">🔥 Top 5 by Clicks</h3>
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
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4">❤️ Top 10 Most Favorited</h3>
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
                    <span class="text-red-400 text-xs">❤️</span>
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
        <h3 class="text-lg font-heading font-bold text-gray-800">📋 Pending Requests</h3>
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
