@extends('layouts.app')

@section('header-title', 'Edit EdTech Badge')
@section('header-subtitle', 'Modify micro-credential settings and criteria')

@section('content')

<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <form method="POST" action="{{ route('admin.badges.update', $badge) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Badge Name</label>
                    <input type="text" name="name" value="{{ old('name', $badge->name) }}" required placeholder="e.g. Canva Classroom Pro" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
                    <select name="category" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                        <option value="tool_mastery" {{ old('category', $badge->category) == 'tool_mastery' ? 'selected' : '' }}>Tool Mastery (Herramientas)</option>
                        <option value="ai_safety" {{ old('category', $badge->category) == 'ai_safety' ? 'selected' : '' }}>AI Safety (Seguridad)</option>
                        <option value="pedagogy" {{ old('category', $badge->category) == 'pedagogy' ? 'selected' : '' }}>Pedagogy (Metodologías)</option>
                        <option value="platform" {{ old('category', $badge->category) == 'platform' ? 'selected' : '' }}>Platform (Uso de AINS)</option>
                    </select>
                    @error('category') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Difficulty -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Difficulty / Metal</label>
                    <select name="difficulty" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                        <option value="bronze" {{ old('difficulty', $badge->difficulty) == 'bronze' ? 'selected' : '' }}>Bronze (Bronce)</option>
                        <option value="silver" {{ old('difficulty', $badge->difficulty) == 'silver' ? 'selected' : '' }}>Silver (Plata)</option>
                        <option value="gold" {{ old('difficulty', $badge->difficulty) == 'gold' ? 'selected' : '' }}>Gold (Oro)</option>
                    </select>
                    @error('difficulty') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Criteria Type -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Criteria Type</label>
                    <select name="criteria_type" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                        <option value="quiz" {{ old('criteria_type', $badge->criteria_type) == 'quiz' ? 'selected' : '' }}>Quiz (Completa un cuestionario)</option>
                        <option value="usage" {{ old('criteria_type', $badge->criteria_type) == 'usage' ? 'selected' : '' }}>Usage (Generar planes o usar sistema)</option>
                        <option value="manual" {{ old('criteria_type', $badge->criteria_type) == 'manual' ? 'selected' : '' }}>Manual (Aprobación manual de Admin)</option>
                    </select>
                    @error('criteria_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Sort Order -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sort Order (Posición)</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $badge->sort_order) }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                    @error('sort_order') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Icon Emoji -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Icon (Emoji)</label>
                    <input type="text" name="icon" value="{{ old('icon', $badge->icon) }}" placeholder="e.g. 🎨, 🤖, 📝" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                    @error('icon') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Color -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Theme Color</label>
                    <div class="flex gap-3 items-center">
                        <input type="color" name="color" id="badge_color_picker" value="{{ old('color', $badge->color) }}" class="w-12 h-11 border border-gray-200 rounded-xl cursor-pointer p-1">
                        <input type="text" name="color_text" id="badge_color_hex" value="{{ old('color', $badge->color) }}" placeholder="#HEX" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none uppercase font-mono">
                    </div>
                    @error('color') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Description / Criteria Requirements</label>
                <textarea name="description" rows="4" required placeholder="Describe what the teacher must achieve or understand to earn this badge. This is displayed publicly." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">{{ old('description', $badge->description) }}</textarea>
                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- ── Evidence / Certification Section ──────────────── -->
            <div class="border border-amber-200 bg-amber-50 rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-bold text-amber-800 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    Evidence & Certification Settings
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2 flex items-center gap-3 p-4 bg-white rounded-xl border border-amber-100">
                        <input type="checkbox" name="requires_evidence" id="requires_evidence" value="1" {{ old('requires_evidence', $badge->requires_evidence) ? 'checked' : '' }} class="w-4 h-4 text-amber-600 rounded">
                        <div>
                            <label for="requires_evidence" class="text-sm font-semibold text-gray-700 cursor-pointer">Requires Evidence Upload</label>
                            <p class="text-xs text-gray-500">Teachers must upload proof (file or URL) before admin can approve and activate this badge.</p>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Certification Program URL <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="url" name="certification_url" value="{{ old('certification_url', $badge->certification_url) }}" placeholder="https://grow.google/certificates/" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 outline-none transition-all">
                        @error('certification_url') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Evidence Instructions <span class="text-gray-400 font-normal">(optional)</span></label>
                        <textarea name="evidence_instructions" rows="2" placeholder="E.g. 'Upload your Google certificate PDF or paste the Credly badge URL.'" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 outline-none transition-all">{{ old('evidence_instructions', $badge->evidence_instructions) }}</textarea>
                        @error('evidence_instructions') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Badge Validity</label>
                        @php
                            $currentDays = old('expires_in_days', $badge->expires_in_days);
                            $presetOptions = ['' => 'Permanent (Never Expires)', '365' => '1 Year', '730' => '2 Years', '1095' => '3 Years'];
                            $isPreset = array_key_exists((string)$currentDays, $presetOptions) || $currentDays === null;
                        @endphp
                        <select id="expiry_preset" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 outline-none transition-all" onchange="handleExpiryPreset(this.value)">
                            <option value="" {{ !$currentDays ? 'selected' : '' }}>Permanent (Never Expires)</option>
                            <option value="365" {{ $currentDays == 365 ? 'selected' : '' }}>1 Year</option>
                            <option value="730" {{ $currentDays == 730 ? 'selected' : '' }}>2 Years</option>
                            <option value="1095" {{ $currentDays == 1095 ? 'selected' : '' }}>3 Years</option>
                            <option value="custom" {{ $currentDays && !$isPreset ? 'selected' : '' }}>Custom (days)</option>
                        </select>
                    </div>
                    <div id="expiry_custom_wrap" class="{{ $currentDays && !$isPreset ? '' : 'hidden' }}">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Custom Expiry (days)</label>
                        <input type="number" name="expires_in_days" id="expires_in_days" min="1" value="{{ $currentDays }}" placeholder="e.g. 540" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 outline-none transition-all">
                        @error('expires_in_days') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <input type="hidden" name="expires_in_days" id="expires_in_days_hidden" value="{{ $isPreset ? $currentDays : '' }}">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-3 bg-ans-dark-green text-white rounded-xl font-semibold hover:bg-ans-seal-green transition-all shadow-md text-sm">Update Badge</button>
                <a href="{{ route('admin.badges.index') }}" class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-semibold hover:bg-gray-200 transition-all text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('badge_color_picker').addEventListener('input', function(e) {
        document.getElementById('badge_color_hex').value = e.target.value;
    });
    document.getElementById('badge_color_hex').addEventListener('input', function(e) {
        let val = e.target.value;
        if(val.startsWith('#') && val.length === 7) {
            document.getElementById('badge_color_picker').value = val;
        }
    });
    function handleExpiryPreset(val) {
        const customWrap = document.getElementById('expiry_custom_wrap');
        const hiddenInput = document.getElementById('expires_in_days_hidden');
        if (val === 'custom') {
            customWrap.classList.remove('hidden');
            hiddenInput.disabled = true;
        } else if (val === '') {
            customWrap.classList.add('hidden');
            hiddenInput.value = '';
            hiddenInput.disabled = false;
            document.getElementById('expires_in_days').value = '';
        } else {
            customWrap.classList.add('hidden');
            hiddenInput.value = val;
            hiddenInput.disabled = false;
            document.getElementById('expires_in_days').value = val;
        }
    }
</script>

@endsection

