@extends('layouts.app')

@section('header-title', 'Suggest New Badge')
@section('header-subtitle', 'Propose a badge you hold or would like to see on the platform')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('badges.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-ans-dark-green transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Back to Badges</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl p-8">
        <form method="POST" action="{{ route('badge-suggestions.store') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Badge Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Microsoft Certified Educator" 
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Description & Requirements</label>
                <textarea name="description" rows="4" required placeholder="Explain what the badge is about and what the teacher must demonstrate to achieve it..." 
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">{{ old('description') }}</textarea>
                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Official Certification Link (Optional)</label>
                <input type="url" name="certification_url" value="{{ old('certification_url') }}" placeholder="https://learn.microsoft.com/en-us/credentials/certifications/..." 
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                @error('certification_url') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-3 bg-ans-dark-green text-white rounded-xl font-bold hover:bg-ans-seal-green transition-all shadow-md text-xs">
                    Send Suggestion
                </button>
                <a href="{{ route('badges.index') }}" class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition-all text-xs">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
