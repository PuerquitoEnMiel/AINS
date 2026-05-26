@extends('layouts.app')

@section('header-title', 'Chatbot Settings')
@section('header-subtitle', 'Configure AINS AI Companion system instructions')

@section('content')

@if(session('success'))
    <div class="max-w-4xl mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2 animate-fade-in-up">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="max-w-4xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <form method="POST" action="{{ route('admin.chatbot-settings.update') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">System Instructions (Prompt)</label>
                <p class="text-xs text-gray-500 mb-3 leading-relaxed">
                    Delimit exactly how the AINS AI Companion should behave, its tone, language, and guidelines. The approved tools list from the database is appended automatically at the end.
                </p>
                <textarea name="instructions" rows="16" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all resize-y leading-relaxed" placeholder="Write system instructions here...">{{ old('instructions', $instructions) }}</textarea>
                @error('instructions') 
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p> 
                @enderror
            </div>

            <div class="bg-ans-light-green/5 border border-ans-light-green/20 rounded-xl p-4 space-y-2 text-xs text-gray-600 leading-relaxed">
                <p class="font-bold text-ans-dark-green flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Pedagogical Guidelines
                </p>
                <ul class="list-disc list-inside space-y-1 pl-2">
                    <li>Instruct the bot to align recommendations with the <strong>SAMR</strong> or <strong>TPACK</strong> frameworks.</li>
                    <li>Tell the bot to provide <strong>Prompt Blueprints</strong> (command blocks) for teachers.</li>
                    <li>Instruct the bot to respond in Spanish or English based on your institutional policy (currently default is English).</li>
                </ul>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-ans-dark-green hover:bg-ans-seal-green text-white rounded-xl font-semibold transition-all shadow-md text-sm">
                    Save Settings
                </button>
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-semibold transition-all text-sm">
                    Back to Dashboard
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
