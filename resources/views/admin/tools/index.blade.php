@extends('layouts.app')

@section('header-title')
    Catalog Management
@endsection
@section('header-subtitle')
    Add, edit or remove tools from the public directory.
@endsection

@section('content')
<div class="relative -mx-8 -mt-8 mb-10 overflow-hidden">
    <div class="bg-gradient-to-br from-ans-blue via-[#002f8a] to-ans-blue px-10 py-12 relative">
        <div class="absolute top-4 right-20 w-28 h-28 bg-white/5 rounded-full blur-2xl animate-float"></div>
        <div class="absolute bottom-2 left-16 w-20 h-20 bg-ans-light-blue/10 rounded-full blur-xl animate-float" style="animation-delay:1s;"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="animate-fade-in-up">
                    <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-4 py-1.5 mb-4 border border-white/10">
                        <svg class="w-3.5 h-3.5 text-ans-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        <span class="text-xs font-semibold text-white/70 uppercase tracking-wider">Tool Catalog</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-heading font-extrabold text-white tracking-tight">Catalog<br><span class="text-ans-light-blue">Management</span></h1>
                    <p class="text-white/50 mt-3 text-sm">{{ $tools->total() }} tools in database</p>
                </div>
                <a href="{{ route('admin.tools.create') }}" class="inline-flex items-center gap-2 bg-ans-orange hover:bg-[#e67600] text-white font-bold px-6 py-3.5 rounded-xl shadow-lg shadow-ans-orange/20 hover:shadow-xl transition-all hover:-translate-y-0.5 self-start md:self-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add New Tool
                </a>
            </div>
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 40" fill="none" class="w-full"><path d="M0 40V0C240 30 480 40 720 40C960 40 1200 30 1440 0V40H0Z" fill="rgb(249 250 251 / 0.5)"/></svg>
    </div>
</div>

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

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up" style="animation-delay:0.1s;">
    <!-- Catalog Toolbar -->
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="relative max-w-xs w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" id="admin-tools-search" placeholder="Search catalog tools..." class="block w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all shadow-sm">
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 font-medium">Quick status:</span>
            <span class="text-xs font-semibold px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100">
                Published
            </span>
            <span class="text-xs font-semibold px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full border border-amber-100">
                Drafts
            </span>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100">
                    <th class="text-left px-6 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider w-12">#</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Tool</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Category</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Type</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Status</th>
                    <th class="text-right px-6 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($tools as $tool)
                <tr class="hover:bg-gray-50/50 transition-colors group">
                    <td class="px-6 py-4 text-gray-400 text-xs font-mono">{{ $tool->id }}</td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-3">
                            @if($tool->logo_url)
                                <img src="{{ str_starts_with($tool->logo_url, 'http') ? $tool->logo_url : asset($tool->logo_url) }}" alt="{{ $tool->name }}" class="w-10 h-10 rounded-xl object-cover border border-gray-100 flex-shrink-0 group-hover:scale-110 transition-transform">
                            @else
                                <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-50 rounded-xl flex items-center justify-center text-gray-400 font-bold text-sm flex-shrink-0 group-hover:from-ans-dark-green/10 group-hover:to-ans-light-green/5 group-hover:text-ans-dark-green transition-all">
                                    {{ substr($tool->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="font-bold text-gray-900 truncate">{{ $tool->name }}</div>
                                <a href="{{ $tool->url }}" target="_blank" class="text-xs text-ans-dark-green hover:underline truncate block max-w-[200px]">{{ $tool->url }}</a>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <span class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full uppercase tracking-wider">{{ $tool->category }}</span>
                    </td>
                    <td class="px-6 py-5">
                        @if($tool->is_google_workspace)
                            <span class="text-[10px] font-bold bg-ans-blue/10 text-ans-blue px-2.5 py-1 rounded-full uppercase tracking-wider">Workspace</span>
                        @else
                            <span class="text-[10px] font-bold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full uppercase tracking-wider">3rd Party</span>
                        @endif
                    </td>
                    <td class="px-6 py-5">
                        <label class="relative inline-flex items-center cursor-pointer group">
                            <input type="checkbox" 
                                   class="sr-only peer status-toggle" 
                                   data-tool-id="{{ $tool->id }}" 
                                   {{ $tool->approval_status === 'approved' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-emerald-500 transition-all"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm peer-checked:translate-x-5 transition-transform"></div>
                            <span class="text-xs font-bold {{ $tool->approval_status === 'approved' ? 'text-emerald-600' : 'text-gray-500' }} uppercase tracking-wider ml-2 select-none peer-checked:text-emerald-600 status-label">
                                {{ $tool->approval_status === 'approved' ? 'Published' : 'Draft' }}
                            </span>
                        </label>
                    </td>
                    <td class="px-6 py-5 text-right">
                        <div class="flex gap-2 justify-end">
                            <a href="{{ route('admin.tools.edit', $tool) }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-ans-dark-green hover:text-white text-gray-600 border border-gray-200 hover:border-ans-dark-green text-xs font-bold px-3 py-2 rounded-xl transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.tools.destroy', $tool) }}" onsubmit="return confirm('Delete {{ $tool->name }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 bg-white hover:bg-red-50 text-gray-400 hover:text-red-600 border border-gray-200 hover:border-red-200 text-xs font-bold px-3 py-2 rounded-xl transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            </div>
                            <p class="text-gray-400 font-medium">No tools yet</p>
                            <a href="{{ route('admin.tools.create') }}" class="text-ans-dark-green text-sm font-semibold hover:underline">Add your first tool →</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tools->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $tools->links() }}
    </div>
    @endif
</div>

<script>
// Status Toggle AJAX
document.querySelectorAll('.status-toggle').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const toolId = this.dataset.toolId;
        const label = this.closest('label').querySelector('.status-label');
        
        label.textContent = 'Updating...';
        label.className = 'text-xs font-bold text-gray-400 uppercase tracking-wider ml-2';
        
        fetch(`/admin/tools/${toolId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const isApproved = data.status === 'approved';
                label.textContent = isApproved ? 'Published' : 'Draft';
                label.className = `text-xs font-bold ${isApproved ? 'text-emerald-600' : 'text-gray-500'} uppercase tracking-wider ml-2`;
            } else {
                alert('Failed to update status.');
                this.checked = !this.checked;
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred.');
            this.checked = !this.checked;
        });
    });
});

// Realtime Search in current page
const adminSearch = document.getElementById('admin-tools-search');
if (adminSearch) {
    adminSearch.addEventListener('input', e => {
        const query = e.target.value.toLowerCase().trim();
        document.querySelectorAll('tbody tr').forEach(row => {
            const nameEl = row.querySelector('.font-bold.text-gray-900');
            if (!nameEl) return;
            
            const name = nameEl.textContent.toLowerCase();
            const cat = row.querySelector('.text-\\[10px\\]').textContent.toLowerCase();
            
            if (name.includes(query) || cat.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}
</script>
@endsection
