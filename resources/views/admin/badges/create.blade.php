@extends('layouts.app')

@section('header-title', 'Create EdTech Badge')
@section('header-subtitle', 'Define a new micro-credential and requirement criteria')

@section('content')

<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <form method="POST" action="{{ route('admin.badges.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Badge Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Canva Classroom Pro" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
                    <select name="category" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                        <option value="tool_mastery" {{ old('category') == 'tool_mastery' ? 'selected' : '' }}>Tool Mastery (Herramientas)</option>
                        <option value="ai_safety" {{ old('category') == 'ai_safety' ? 'selected' : '' }}>AI Safety (Seguridad)</option>
                        <option value="pedagogy" {{ old('category') == 'pedagogy' ? 'selected' : '' }}>Pedagogy (Metodologías)</option>
                        <option value="platform" {{ old('category') == 'platform' ? 'selected' : '' }}>Platform (Uso de AINS)</option>
                    </select>
                    @error('category') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Difficulty -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Difficulty / Metal</label>
                    <select name="difficulty" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                        <option value="bronze" {{ old('difficulty') == 'bronze' ? 'selected' : '' }}>Bronze (Bronce)</option>
                        <option value="silver" {{ old('difficulty') == 'silver' ? 'selected' : '' }}>Silver (Plata)</option>
                        <option value="gold" {{ old('difficulty') == 'gold' ? 'selected' : '' }}>Gold (Oro)</option>
                    </select>
                    @error('difficulty') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Criteria Type -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Criteria Type</label>
                    <select name="criteria_type" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                        <option value="quiz" {{ old('criteria_type') == 'quiz' ? 'selected' : '' }}>Quiz (Completa un cuestionario)</option>
                        <option value="usage" {{ old('criteria_type') == 'usage' ? 'selected' : '' }}>Usage (Generar planes o usar sistema)</option>
                        <option value="manual" {{ old('criteria_type') == 'manual' ? 'selected' : '' }}>Manual (Aprobación manual de Admin)</option>
                    </select>
                    @error('criteria_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Sort Order -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sort Order (Posición)</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 10) }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                    @error('sort_order') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Icon Emoji -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Icon (Emoji)</label>
                    <input type="text" name="icon" value="{{ old('icon', '🏅') }}" placeholder="e.g. 🎨, 🤖, 📝" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                    @error('icon') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Color -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Theme Color</label>
                    <div class="flex gap-3 items-center">
                        <input type="color" name="color" id="badge_color_picker" value="{{ old('color', '#0f5A47') }}" class="w-12 h-11 border border-gray-200 rounded-xl cursor-pointer p-1">
                        <input type="text" name="color_text" id="badge_color_hex" value="{{ old('color', '#0f5A47') }}" placeholder="#HEX" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none uppercase font-mono">
                    </div>
                    @error('color') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Description / Criteria Requirements</label>
                    <textarea name="description" rows="4" required placeholder="Describe what the teacher must achieve or understand to earn this badge. This is displayed publicly." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">{{ old('description') }}</textarea>
                    @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-3 bg-ans-dark-green text-white rounded-xl font-semibold hover:bg-ans-seal-green transition-all shadow-md text-sm">Create Badge</button>
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
</script>

@endsection
