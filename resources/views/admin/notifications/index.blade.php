@extends('layouts.app')

@section('header-title', 'Security & Events Telemetry')
@section('header-subtitle', 'Real-time security logs, badge suggestions, and evidence uploads')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex justify-between items-center bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-ans-dark-green/10 flex items-center justify-center text-ans-dark-green">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Admin Telemetry</h3>
                <p class="text-xs text-gray-400">Keep track of teacher activity and prompt injections</p>
            </div>
        </div>
        
        @if($notifications->whereNull('read_at')->count() > 0)
        <form method="POST" action="{{ route('admin.notifications.readAll') }}">
            @csrf
            <button type="submit" class="px-4 py-2 bg-ans-dark-green text-white text-xs font-semibold rounded-xl hover:bg-ans-dark-green/95 transition-all shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Mark All as Read
            </button>
        </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-50">
            @forelse($notifications as $notif)
            <div class="p-6 hover:bg-gray-50/50 transition-colors {{ !$notif->read_at ? 'bg-blue-50/10 border-l-4 border-blue-500' : 'border-l-4 border-transparent' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center
                            @if($notif->type === 'security') bg-red-100 text-red-600 
                            @elseif($notif->type === 'evidence') bg-emerald-100 text-emerald-600 
                            @else bg-amber-100 text-amber-600 @endif">
                            @if($notif->type === 'security')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            @elseif($notif->type === 'evidence')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                            @endif
                        </div>
                        
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h4 class="font-semibold text-gray-800 text-sm">{{ $notif->title }}</h4>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full
                                    @if($notif->type === 'security') bg-red-100 text-red-700 
                                    @elseif($notif->type === 'evidence') bg-emerald-100 text-emerald-700 
                                    @else bg-amber-100 text-amber-700 @endif">
                                    {{ $notif->type }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600">{{ $notif->message }}</p>
                            
                            @if($notif->type === 'security' && isset($notif->data['prompt']))
                                <div class="mt-2 bg-gray-50 border border-gray-100 p-3 rounded-xl max-w-2xl">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Blocked Prompt:</p>
                                    <p class="text-xs font-mono text-red-600 bg-red-50/50 p-2 rounded border border-red-100 break-words">"{{ $notif->data['prompt'] }}"</p>
                                    @if(isset($notif->data['category']) || isset($notif->data['reason']))
                                        <p class="text-[10px] mt-2 text-gray-500">
                                            <strong>Category:</strong> {{ $notif->data['category'] ?? 'N/A' }} | 
                                            <strong>Reason:</strong> {{ $notif->data['reason'] ?? 'N/A' }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if($notif->type === 'evidence')
                                <div class="mt-2 flex gap-2">
                                    <a href="{{ route('admin.badge-evidence.index') }}" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline font-semibold">
                                        Go to Review Queue →
                                    </a>
                                </div>
                            @endif

                            @if($notif->type === 'suggestion')
                                <div class="mt-2 flex gap-2">
                                    <a href="{{ route('admin.badge-suggestions.index') }}" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline font-semibold">
                                        Go to Suggestions Queue →
                                    </a>
                                </div>
                            @endif

                            <div class="flex items-center gap-2 pt-2 text-[10px] text-gray-400">
                                <span class="font-medium text-gray-500">{{ $notif->user ? $notif->user->name : 'System' }}</span>
                                <span>•</span>
                                <span>{{ $notif->created_at->diffForHumans() }}</span>
                                @if($notif->read_at)
                                    <span>•</span>
                                    <span class="text-gray-400 flex items-center gap-0.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Read {{ $notif->read_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(!$notif->read_at)
                    <form method="POST" action="{{ route('admin.notifications.read', $notif) }}">
                        @csrf
                        <button type="submit" class="p-1 text-gray-400 hover:text-ans-dark-green rounded-lg hover:bg-gray-100 transition-colors" title="Mark as read">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-6 py-16 text-center">
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <p class="text-gray-400 font-medium">No notifications yet</p>
                    <p class="text-xs text-gray-300 font-normal">All clear! We haven't logged any security breaches or user requests yet.</p>
                </div>
            </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
