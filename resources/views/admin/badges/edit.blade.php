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
                        <option value="tool_mastery" {{ old('category', $badge->category) == 'tool_mastery' ? 'selected' : '' }}>Tool Mastery</option>
                        <option value="ai_safety" {{ old('category', $badge->category) == 'ai_safety' ? 'selected' : '' }}>AI Safety</option>
                        <option value="pedagogy" {{ old('category', $badge->category) == 'pedagogy' ? 'selected' : '' }}>Pedagogy</option>
                        <option value="platform" {{ old('category', $badge->category) == 'platform' ? 'selected' : '' }}>Platform</option>
                    </select>
                    @error('category') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Difficulty -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Difficulty / Metal</label>
                    <select name="difficulty" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                        <option value="bronze" {{ old('difficulty', $badge->difficulty) == 'bronze' ? 'selected' : '' }}>Bronze</option>
                        <option value="silver" {{ old('difficulty', $badge->difficulty) == 'silver' ? 'selected' : '' }}>Silver</option>
                        <option value="gold" {{ old('difficulty', $badge->difficulty) == 'gold' ? 'selected' : '' }}>Gold</option>
                    </select>
                    @error('difficulty') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Sort Order -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sort Order</label>
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
                    <!-- Validity Duration -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Duración de Validez <span class="text-gray-400 font-normal">(dejar vacío = permanente)</span></label>
                        <div class="flex gap-3 items-center">
                            <input type="number" name="validity_days" id="validity_days" value="{{ old('validity_days', $badge->validity_days) }}" placeholder="Ej: 1095" min="1" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 outline-none transition-all">
                            <span class="text-xs text-gray-400 whitespace-nowrap" id="validity_label">días</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <button type="button" onclick="setValidity(365)" class="px-3 py-1 text-[11px] font-medium bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 transition-all">1 Año</button>
                            <button type="button" onclick="setValidity(730)" class="px-3 py-1 text-[11px] font-medium bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 transition-all">2 Años</button>
                            <button type="button" onclick="setValidity(1095)" class="px-3 py-1 text-[11px] font-medium bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 transition-all">3 Años</button>
                            <button type="button" onclick="setValidity(1825)" class="px-3 py-1 text-[11px] font-medium bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 transition-all">5 Años</button>
                            <button type="button" onclick="setValidity('')" class="px-3 py-1 text-[11px] font-medium bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition-all">Permanente</button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Cada docente tendrá su propia fecha de expiración individual, contada desde que se le aprueba la evidencia.</p>
                        @error('validity_days') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
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

    function setValidity(days) {
        document.getElementById('validity_days').value = days;
        updateValidityLabel(days);
    }

    function updateValidityLabel(days) {
        const label = document.getElementById('validity_label');
        if (!days || days === '') { label.textContent = 'permanente'; return; }
        days = parseInt(days);
        if (days % 365 === 0) { label.textContent = '= ' + (days/365) + (days/365 === 1 ? ' año' : ' años'); }
        else if (days % 30 === 0) { label.textContent = '= ' + (days/30) + (days/30 === 1 ? ' mes' : ' meses'); }
        else { label.textContent = 'días'; }
    }

    document.getElementById('validity_days').addEventListener('input', function(e) {
        updateValidityLabel(e.target.value);
    });

    // Auto-init label on page load
    updateValidityLabel(document.getElementById('validity_days').value);
</script>

@endsection

