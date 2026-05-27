@extends('layouts.app')

@section('header-title', 'Submit Badge Evidence')
@section('header-subtitle', 'Upload your certification proof for admin review')

@section('content')

<div class="max-w-2xl">
    <!-- Badge Info Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl shadow-inner"
                 style="background-color: {{ $badge->color }}22; border: 2px solid {{ $badge->color }}44;">
                {{ $badge->icon }}
            </div>
            <div>
                <h2 class="font-bold text-gray-900 text-lg">{{ $badge->name }}</h2>
                <p class="text-sm text-gray-500">{{ $badge->description }}</p>
                <div class="flex items-center gap-3 mt-2">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide bg-gray-100 text-gray-600">{{ $badge->difficulty }}</span>
                    @if($badge->hasExpiry())
                    <span class="text-xs text-amber-600 font-medium">⏱ Validity: {{ $badge->validityLabel() }}</span>
                    @else
                    <span class="text-xs text-green-600 font-medium">🟢 Permanent (No Expiration)</span>
                    @endif
                </div>
            </div>
        </div>
        @if($badge->certification_url)
        <a href="{{ $badge->certification_url }}" target="_blank" class="mt-4 flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            Visit official certification program
        </a>
        @endif
    </div>

    @if($existing)
    <div class="mb-6 p-4 rounded-2xl border
        {{ $existing->isPending() ? 'bg-amber-50 border-amber-200' : ($existing->isApproved() ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200') }}">
        <div class="flex items-center gap-3">
            <span class="text-2xl">
                @if($existing->isPending()) ⏳ @elseif($existing->isApproved()) ✅ @else ❌ @endif
            </span>
            <div>
                <p class="font-semibold text-sm text-gray-800">Status: {{ $existing->status === 'pending' ? 'Pending' : ($existing->status === 'approved' ? 'Approved' : 'Rejected') }}</p>
                @if($existing->admin_notes)
                <p class="text-sm text-gray-600 mt-1">Admin notes: "{{ $existing->admin_notes }}"</p>
                @endif
                @if($existing->isApproved() && $existing->expires_at)
                <p class="text-xs text-green-700 mt-1">Your badge expires on {{ $existing->expires_at->format('m/d/Y') }}</p>
                @endif
            </div>
        </div>
        @if($existing->isRejected())
        <p class="text-xs text-red-700 mt-2 font-medium">You can upload your corrected evidence again below.</p>
        @elseif($existing->isPending())
        <p class="text-xs text-amber-700 mt-2">The administrator will review your evidence soon. You can update your submission if needed.</p>
        @endif
    </div>
    @endif

    @if($badge->evidence_instructions)
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-2xl">
        <p class="text-sm font-semibold text-blue-800 mb-1">📋 Instructions</p>
        <p class="text-sm text-blue-700">{{ $badge->evidence_instructions }}</p>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-2xl text-sm text-green-700 font-medium">✅ {{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl">
        <ul class="text-sm text-red-600 space-y-1">@foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    @if(!$existing || !$existing->isApproved())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-ans-dark-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            Upload Evidence
        </h3>
        <form method="POST" action="{{ route('badge-evidence.store', $badge) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Certificate URL -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Certificate URL <span class="text-gray-400 font-normal">(Credly, Google, etc.)</span></label>
                <input type="url" name="certificate_url" value="{{ old('certificate_url', $existing?->certificate_url) }}"
                       placeholder="https://www.credly.com/badges/..."
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                <p class="text-xs text-gray-400 mt-1">Paste the public link of your digital certificate (Credly, Google Certificates, LinkedIn, etc.)</p>
            </div>

            <!-- File Upload -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Evidence File <span class="text-gray-400 font-normal">(PDF, JPG, PNG — max 5MB)</span></label>
                <div id="file-drop-zone" class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-ans-dark-green/40 transition-colors cursor-pointer"
                     onclick="document.getElementById('evidence_file').click()">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <p class="text-sm text-gray-500" id="file-label">Drag a file or click to select</p>
                    <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG up to 5MB</p>
                </div>
                <input type="file" name="evidence_file" id="evidence_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden"
                       onchange="document.getElementById('file-label').textContent = this.files[0]?.name ?? 'Drag a file or click to select'">
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Additional notes <span class="text-gray-400 font-normal">(optional)</span></label>
                <textarea name="notes" rows="3" placeholder="Tell us when and how you obtained this certification..." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">{{ old('notes', $existing?->notes) }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button type="submit" class="px-6 py-3 bg-ans-dark-green text-white rounded-xl font-semibold hover:bg-ans-seal-green transition-all shadow-md text-sm">
                    Submit for Review
                </button>
                <a href="{{ route('badges.show', $badge->slug) }}" class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-semibold hover:bg-gray-200 transition-all text-sm">Cancel</a>
            </div>
        </form>
    </div>
    @endif
</div>

@endsection
