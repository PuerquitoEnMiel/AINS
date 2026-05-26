@extends('layouts.app')

@section('header-title', 'Badge Evidence Review')
@section('header-subtitle', 'Approve or reject teacher certification submissions')

@section('content')

@if($pendingCount ?? 0 > 0)
<div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-3">
    <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
    <p class="text-sm font-semibold text-amber-800">{{ $evidences->where('status', 'pending')->count() }} submission(s) pending your review</p>
</div>
@endif

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Teacher</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Badge</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Evidence</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Submitted</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($evidences as $evidence)
                <tr class="hover:bg-gray-50/50 transition-colors {{ $evidence->isPending() ? 'bg-amber-50/30' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-ans-dark-green/10 flex items-center justify-center text-xs font-bold text-ans-dark-green">
                                {{ strtoupper(substr($evidence->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $evidence->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $evidence->user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">{{ $evidence->badge->icon }}</span>
                            <span class="font-medium text-gray-800">{{ $evidence->badge->name }}</span>
                        </div>
                        @if($evidence->badge->hasExpiry())
                        <p class="text-xs text-gray-400 mt-0.5">Duración: {{ $evidence->badge->validityLabel() }} desde aprobación</p>
                        @else
                        <p class="text-xs text-green-600 mt-0.5">Insignia permanente</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="space-y-1">
                            @if($evidence->certificate_url)
                            <a href="{{ $evidence->certificate_url }}" target="_blank" class="flex items-center gap-1.5 text-blue-600 hover:text-blue-700 text-xs font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                View Certificate URL
                            </a>
                            @endif
                            @if($evidence->file_path)
                            <a href="{{ asset('storage/' . $evidence->file_path) }}" target="_blank" class="flex items-center gap-1.5 text-gray-600 hover:text-gray-800 text-xs font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                {{ $evidence->file_name }}
                            </a>
                            @endif
                            @if($evidence->notes)
                            <p class="text-xs text-gray-500 italic">"{{ Str::limit($evidence->notes, 80) }}"</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                              style="background-color: {{ $evidence->statusColor() }}22; color: {{ $evidence->statusColor() }}">
                            @if($evidence->isPending())
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                            @endif
                            {{ $evidence->status === 'pending' ? 'Pending' : ($evidence->status === 'approved' ? 'Approved' : 'Rejected') }}
                        </span>
                        @if($evidence->isApproved() && $evidence->expires_at)
                        <p class="text-xs text-gray-400 mt-1">Expires {{ $evidence->expires_at->format('d M Y') }}</p>
                        @endif
                        @if($evidence->admin_notes)
                        <p class="text-xs text-gray-500 mt-1 italic">{{ Str::limit($evidence->admin_notes, 60) }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $evidence->created_at->diffForHumans() }}</td>
                    <td class="px-6 py-4">
                        @if($evidence->isPending())
                        <div class="flex flex-col gap-2 min-w-[200px]">
                            <form method="POST" action="{{ route('admin.badge-evidence.approve', $evidence) }}" class="space-y-2">
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                        Fecha de Certificación
                                    </label>
                                    <input type="date" name="certified_at" value="{{ date('Y-m-d') }}" 
                                           class="w-full px-2 py-1 text-xs border border-gray-200 rounded-lg focus:ring-1 focus:ring-green-500 focus:border-green-500 outline-none transition-all">
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="flex-1 px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition-all text-center">
                                        ✓ Approve
                                    </button>
                                    <button type="button" onclick="showRejectModal({{ $evidence->id }})" class="px-3 py-1.5 bg-red-100 text-red-700 text-xs font-semibold rounded-lg hover:bg-red-200 transition-all">
                                        ✗ Reject
                                    </button>
                                </div>
                            </form>
                        </div>
                        <!-- Hidden reject form -->
                        <form id="reject-form-{{ $evidence->id }}" method="POST" action="{{ route('admin.badge-evidence.reject', $evidence) }}" class="hidden mt-2 space-y-2">
                            @csrf
                            <textarea name="admin_notes" rows="2" placeholder="Reason for rejection..." required class="w-full px-3 py-2 border border-red-200 rounded-lg text-xs focus:ring-2 focus:ring-red-300 outline-none"></textarea>
                            <div class="flex gap-2">
                                <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition-all">Send Rejection</button>
                                <button type="button" onclick="document.getElementById('reject-form-{{ $evidence->id }}').classList.add('hidden')" class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
                            </div>
                        </form>
                        @elseif($evidence->isApproved())
                        <span class="text-xs text-green-700 font-medium">✓ Approved by {{ $evidence->reviewer?->name ?? 'Admin' }}</span>
                        @else
                        <span class="text-xs text-red-600 font-medium">✗ Rejected</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            <p class="text-gray-400 font-medium">No evidence submissions yet</p>
                            <p class="text-xs text-gray-300">When teachers submit certification evidence, it will appear here for review.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($evidences->hasPages())
    <div class="p-4 border-t border-gray-100">{{ $evidences->links() }}</div>
    @endif
</div>

<script>
function showRejectModal(id) {
    document.getElementById('reject-form-' + id).classList.toggle('hidden');
}
</script>

@endsection
