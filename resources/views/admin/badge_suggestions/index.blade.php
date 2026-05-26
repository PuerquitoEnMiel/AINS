@extends('layouts.app')

@section('header-title', 'Badge Suggestions Queue')
@section('header-subtitle', 'Review and act on teacher-proposed badges')

@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-medium">✅ {{ session('success') }}</div>
@endif

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Teacher</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Suggested Badge</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Submitted</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($suggestions as $suggestion)
                <tr class="hover:bg-gray-50/50 transition-colors {{ $suggestion->isPending() ? 'bg-amber-50/30' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-ans-dark-green/10 flex items-center justify-center text-xs font-bold text-ans-dark-green">
                                {{ strtoupper(substr($suggestion->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $suggestion->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $suggestion->user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-800">{{ $suggestion->name }}</p>
                        @if($suggestion->certification_url)
                        <a href="{{ $suggestion->certification_url }}" target="_blank" class="flex items-center gap-1 text-blue-600 hover:text-blue-700 text-xs font-medium mt-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            Official URL
                        </a>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs text-gray-600 leading-relaxed max-w-sm">{{ $suggestion->description }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColor = match($suggestion->status) {
                                'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
                                default => 'bg-gray-100 text-gray-800 border-gray-200'
                            };
                            $statusText = match($suggestion->status) {
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                default => 'Unknown'
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold capitalize border {{ $statusColor }}">
                            {{ $statusText }}
                        </span>
                        @if($suggestion->admin_notes)
                        <p class="text-xs text-gray-500 mt-1 italic">Notes: "{{ $suggestion->admin_notes }}"</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $suggestion->created_at->diffForHumans() }}</td>
                    <td class="px-6 py-4">
                        @if($suggestion->isPending())
                        <div class="flex flex-col gap-2 min-w-[200px]">
                            <!-- Admin notes input -->
                            <input type="text" id="notes-{{ $suggestion->id }}" placeholder="Admin notes (optional for approval)..." class="px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs outline-none focus:ring-1 focus:ring-ans-dark-green focus:border-ans-dark-green transition-all">
                            
                            <div class="flex gap-2">
                                <!-- Approve form -->
                                <form method="POST" action="{{ route('admin.badge-suggestions.approve', $suggestion) }}" onsubmit="this.admin_notes.value = document.getElementById('notes-{{ $suggestion->id }}').value;" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="admin_notes">
                                    <button type="submit" class="w-full px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition-all text-center">
                                        ✓ Approve
                                    </button>
                                </form>

                                <!-- Reject form -->
                                <form method="POST" action="{{ route('admin.badge-suggestions.reject', $suggestion) }}" onsubmit="
                                    var val = document.getElementById('notes-{{ $suggestion->id }}').value;
                                    if(!val.trim()){
                                        alert('Please add a reason in the notes field before rejecting.');
                                        return false;
                                    }
                                    this.admin_notes.value = val;
                                " class="flex-1">
                                    @csrf
                                    <input type="hidden" name="admin_notes">
                                    <button type="submit" class="w-full px-3 py-1.5 bg-red-100 text-red-700 text-xs font-semibold rounded-lg hover:bg-red-200 transition-all text-center">
                                        ✗ Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                        @else
                        <span class="text-xs text-gray-400 font-medium">Completed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                            <p class="text-gray-400 font-medium">No badge suggestions yet</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suggestions->hasPages())
    <div class="p-4 border-t border-gray-100">{{ $suggestions->links() }}</div>
    @endif
</div>

@endsection
