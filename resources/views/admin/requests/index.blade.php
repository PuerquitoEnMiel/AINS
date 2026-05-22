@extends('layouts.app')

@section('header-title')
    <svg class="w-5 h-5 inline-block mr-1 -mt-0.5 text-ans-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
    Admin Panel
@endsection

@section('header-subtitle')
    Review and manage tool suggestions submitted by teachers.
@endsection

@section('content')

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- HERO BANNER — Admin                                        -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="relative -mx-8 -mt-8 mb-12 overflow-hidden">
    <div class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 px-10 py-12 relative">
        <!-- Decorative shapes -->
        <div class="absolute top-4 right-20 w-28 h-28 bg-ans-orange/10 rounded-full blur-2xl animate-float"></div>
        <div class="absolute bottom-2 left-16 w-20 h-20 bg-ans-dark-green/10 rounded-full blur-xl animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-ans-purple/10 rounded-full blur-lg animate-pulse-soft"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8">
                <!-- Left -->
                <div class="animate-fade-in-up" style="animation-duration: 0.5s;">
                    <div class="inline-flex items-center gap-2 bg-white/5 backdrop-blur-sm rounded-full px-4 py-1.5 mb-5 border border-white/10">
                        <svg class="w-3.5 h-3.5 text-ans-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span class="text-xs font-semibold text-white/70 tracking-wide uppercase">Admin Access</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-heading font-extrabold text-white tracking-tight">
                        Administration<br>
                        <span class="text-ans-orange">Panel</span>
                    </h1>
                    <p class="text-white/50 mt-3 max-w-lg text-sm">
                        Review, approve or reject tool suggestions submitted by faculty members.
                    </p>
                </div>

                <!-- Right: Stats Cards -->
                <div class="flex gap-4 animate-fade-in-up" style="animation-duration: 0.7s;">
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-5 text-center min-w-[110px] hover:bg-white/10 transition-colors">
                        <div class="text-3xl font-heading font-extrabold text-ans-orange">{{ $requests->where('status','pending')->count() }}</div>
                        <p class="text-xs text-white/50 mt-1 font-medium">Pending</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-5 text-center min-w-[110px] hover:bg-white/10 transition-colors">
                        <div class="text-3xl font-heading font-extrabold text-emerald-400">{{ $requests->where('status','approved')->count() }}</div>
                        <p class="text-xs text-white/50 mt-1 font-medium">Approved</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-5 text-center min-w-[110px] hover:bg-white/10 transition-colors">
                        <div class="text-3xl font-heading font-extrabold text-gray-500">{{ $requests->where('status','rejected')->count() }}</div>
                        <p class="text-xs text-white/50 mt-1 font-medium">Rejected</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 40" fill="none" class="w-full">
            <path d="M0 40V0C240 30 480 40 720 40C960 40 1200 30 1440 0V40H0Z" fill="rgb(249 250 251 / 0.5)"/>
        </svg>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- ALERTS                                                     -->
<!-- ═══════════════════════════════════════════════════════════ -->
@if(session('success'))
<div class="mb-6 animate-fade-in-up">
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 flex items-center gap-3">
        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
</div>
@endif
@if(session('info'))
<div class="mb-6 animate-fade-in-up">
    <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-2xl p-4 flex items-center gap-3">
        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="text-sm font-medium">{{ session('info') }}</p>
    </div>
</div>
@endif

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- REQUESTS TABLE — Premium card-based table                  -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="animate-fade-in-up" style="animation-delay: 0.15s;">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-8 bg-gradient-to-b from-gray-800 to-gray-400 rounded-full"></div>
            <div>
                <h3 class="text-xl font-heading font-bold text-gray-900">All Requests</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ $requests->count() }} total submissions</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Requests Toolbar -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="relative max-w-xs w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" id="admin-requests-search" placeholder="Search requests..." class="block w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-ans-orange/20 focus:border-ans-orange transition-all shadow-sm">
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400 font-medium">Quick filter:</span>
                <button onclick="filterRequests('all')" class="text-xs font-semibold px-3 py-1 bg-gray-800 text-white rounded-full hover:bg-gray-700 transition-all select-none focus:outline-none filter-pill active-filter" data-filter="all">All</button>
                <button onclick="filterRequests('pending')" class="text-xs font-semibold px-3 py-1 bg-white text-amber-600 border border-amber-100 rounded-full hover:bg-amber-50/50 transition-all select-none focus:outline-none filter-pill" data-filter="pending">Pending</button>
                <button onclick="filterRequests('approved')" class="text-xs font-semibold px-3 py-1 bg-white text-emerald-600 border border-emerald-100 rounded-full hover:bg-emerald-50/50 transition-all select-none focus:outline-none filter-pill" data-filter="approved">Approved</button>
                <button onclick="filterRequests('rejected')" class="text-xs font-semibold px-3 py-1 bg-white text-gray-400 border border-gray-100 rounded-full hover:bg-gray-50 transition-all select-none focus:outline-none filter-pill" data-filter="rejected">Rejected</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="text-left px-6 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Tool</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Submitted By</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Category</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Status</th>
                        <th class="text-right px-6 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($requests as $req)
                    <tr class="hover:bg-gray-50/50 transition-colors group request-row" data-status="{{ $req->status }}">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-50 rounded-xl flex items-center justify-center text-gray-400 font-bold text-sm flex-shrink-0 group-hover:from-ans-dark-green/10 group-hover:to-ans-light-green/5 group-hover:text-ans-dark-green transition-all">
                                    {{ substr($req->tool_name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-gray-900 truncate">{{ $req->tool_name }}</div>
                                    <a href="{{ $req->url }}" target="_blank" class="text-xs text-ans-dark-green hover:underline truncate block max-w-[200px]">{{ $req->url }}</a>
                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $req->description }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="font-semibold text-gray-800 text-sm">{{ $req->requester_name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $req->requester_email }}</div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full uppercase tracking-wider">{{ $req->category }}</span>
                            @if($req->is_google_workspace)
                            <span class="text-[10px] font-bold bg-ans-blue/10 text-ans-blue px-2.5 py-1 rounded-full uppercase tracking-wider ml-1">Workspace</span>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            @if($req->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-amber-50 text-amber-600 px-3 py-1.5 rounded-full uppercase tracking-wider border border-amber-100">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse-soft"></span>
                                Pending
                            </span>
                            @elseif($req->status === 'approved')
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-full uppercase tracking-wider border border-emerald-100">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Approved
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-gray-100 text-gray-400 px-3 py-1.5 rounded-full uppercase tracking-wider border border-gray-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Rejected
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right">
                            @if($req->status === 'pending')
                            <div class="flex gap-2 justify-end">
                                <form method="POST" action="{{ route('admin.requests.approve', $req) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 bg-ans-dark-green hover:bg-ans-seal-green text-white text-xs font-bold px-4 py-2 rounded-xl shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.requests.reject', $req) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 bg-white hover:bg-red-50 text-gray-500 hover:text-red-600 border border-gray-200 hover:border-red-200 text-xs font-bold px-4 py-2 rounded-xl transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Reject
                                    </button>
                                </form>
                            </div>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <p class="text-gray-400 font-medium">No requests yet</p>
                                <p class="text-xs text-gray-300">Suggestions from teachers will appear here.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
    </div>
</div>

<script>
let currentStatusFilter = 'all';

function filterRequests(status) {
    currentStatusFilter = status;
    
    // Update active filter class
    document.querySelectorAll('.filter-pill').forEach(btn => {
        if (btn.dataset.filter === status) {
            btn.className = `text-xs font-semibold px-3 py-1 bg-gray-800 text-white rounded-full transition-all select-none focus:outline-none filter-pill`;
        } else {
            let textColor = 'text-gray-400 border-gray-100';
            if (btn.dataset.filter === 'pending') textColor = 'text-amber-600 border-amber-100';
            if (btn.dataset.filter === 'approved') textColor = 'text-emerald-600 border-emerald-100';
            btn.className = `text-xs font-semibold px-3 py-1 bg-white ${textColor} border rounded-full hover:bg-gray-50 transition-all select-none focus:outline-none filter-pill`;
        }
    });
    
    applyFilters();
}

function applyFilters() {
    const query = document.getElementById('admin-requests-search').value.toLowerCase().trim();
    
    document.querySelectorAll('.request-row').forEach(row => {
        const rowStatus = row.dataset.status;
        const name = row.querySelector('.font-bold.text-gray-900').textContent.toLowerCase();
        const desc = row.querySelector('p.text-gray-400') ? row.querySelector('p.text-gray-400').textContent.toLowerCase() : '';
        const requester = row.querySelector('.text-gray-800.text-sm').textContent.toLowerCase();
        const category = row.querySelector('.text-\\[10px\\]').textContent.toLowerCase();
        
        const matchesStatus = currentStatusFilter === 'all' || rowStatus === currentStatusFilter;
        const matchesSearch = name.includes(query) || desc.includes(query) || requester.includes(query) || category.includes(query);
        
        if (matchesStatus && matchesSearch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

const reqSearchInput = document.getElementById('admin-requests-search');
if (reqSearchInput) {
    reqSearchInput.addEventListener('input', applyFilters);
}
</script>
@endsection
