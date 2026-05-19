@extends('layouts.app')

@section('header-title', 'Admin Dashboard')
@section('header-subtitle', 'Platform analytics and management overview')

@section('content')

<!-- ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<!-- ── Summary Stats Cards ───────────────────────────────────── -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    @php
        $statCards = [
            ['label' => 'Total Users', 'value' => $stats['total_users'], 'icon' => '👥', 'color' => 'from-emerald-500 to-green-600'],
            ['label' => 'Approved Tools', 'value' => $stats['total_tools'], 'icon' => '🔧', 'color' => 'from-blue-500 to-indigo-600'],
            ['label' => 'Pending Requests', 'value' => $stats['pending_requests'], 'icon' => '📋', 'color' => 'from-amber-500 to-orange-600'],
            ['label' => 'Conversations', 'value' => $stats['total_chats'], 'icon' => '💬', 'color' => 'from-purple-500 to-violet-600'],
            ['label' => 'Reviews', 'value' => $stats['total_reviews'], 'icon' => '⭐', 'color' => 'from-yellow-500 to-amber-600'],
            ['label' => 'Tool Views', 'value' => $stats['total_views'], 'icon' => '👁️', 'color' => 'from-rose-500 to-pink-600'],
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

<!-- ── Charts Row ────────────────────────────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Activity Chart (2 cols) -->
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

<!-- ── Second Row: Category Distribution + Top Tools ─────────── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Category Distribution Bar -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4">🏷️ Tools by Category</h3>
        <div id="category-chart"></div>
    </div>

    <!-- Top 5 Popular Tools -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-heading font-bold text-gray-800 mb-4">🔥 Top 5 Popular Tools</h3>
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
                        <span>{{ $tool->click_count }} clicks</span>
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
    // Activity Area Chart
    const activityData = @json($activityChart);
    new ApexCharts(document.querySelector('#activity-chart'), {
        chart: { type: 'area', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
        series: [{ name: 'Views', data: Object.values(activityData) }],
        xaxis: {
            categories: Object.keys(activityData).map(d => {
                const date = new Date(d);
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

    // Category Bar Chart
    const catData = @json($categoryDistribution);
    new ApexCharts(document.querySelector('#category-chart'), {
        chart: { type: 'bar', height: 260, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
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
