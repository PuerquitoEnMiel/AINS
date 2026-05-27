@extends('layouts.app')

@section('header-title', 'Notification Templates')
@section('header-subtitle', 'Configure email alert subjects and text templates dynamically')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-ans-dark-green/10 flex items-center justify-center text-ans-dark-green">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Email Alerts Customization</h3>
            <p class="text-xs text-gray-400">Edit message templates. Placeholders: <code>{user}</code>, <code>{badge}</code>, <code>{suggestion}</code>.</p>
        </div>
    </div>

    <div class="grid gap-6">
        @foreach($templates as $tmpl)
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:border-ans-dark-green/20 transition-all">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full
                        @if($tmpl->type === 'security') bg-red-100 text-red-700 
                        @elseif($tmpl->type === 'evidence') bg-emerald-100 text-emerald-700 
                        @else bg-amber-100 text-amber-700 @endif">
                        {{ $tmpl->type }}
                    </span>
                    <h4 class="font-semibold text-gray-800 text-sm">{{ $tmpl->subject }}</h4>
                </div>
                <p class="text-xs text-gray-600 bg-gray-50 p-3 rounded-xl font-mono border border-gray-100">
                    {{ $tmpl->template }}
                </p>
            </div>
            
            <a href="{{ route('admin.notification-templates.edit', $tmpl) }}" class="px-4 py-2 bg-ans-dark-green text-white text-xs font-semibold rounded-xl hover:bg-ans-dark-green/95 transition-all shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                Edit Template
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
