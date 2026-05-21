@extends('layouts.app')

@section('header-title')
    Suggest a Tool
@endsection

@section('header-subtitle')
    Help us grow the AI tools catalog for everyone at ANS.
@endsection

@section('content')

<!-- HERO BANNER — Suggest Tool -->
<div class="relative -mx-8 -mt-8 mb-12 overflow-hidden">
    <div class="bg-gradient-to-br from-ans-purple via-[#5a2570] to-ans-purple px-10 py-12 relative">
        <div class="absolute top-4 right-20 w-28 h-28 bg-white/5 rounded-full blur-2xl animate-float"></div>
        <div class="absolute bottom-2 left-16 w-20 h-20 bg-ans-orange/10 rounded-full blur-xl animate-float" style="animation-delay: 1.2s;"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <a href="/" class="inline-flex items-center gap-2 text-white/60 hover:text-white text-sm font-medium transition-colors mb-6 group">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back to catalog
            </a>
            <div class="flex items-center gap-4 animate-fade-in-up" style="animation-duration: 0.5s;">
                <div class="w-14 h-14 bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-ans-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <div>
                    <h1 class="text-3xl md:text-4xl font-heading font-extrabold text-white tracking-tight">Suggest an AI Tool</h1>
                    <p class="text-white/60 mt-1">Know a tool that should be in the catalog? Submit your suggestion to the admin team.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 40" fill="none" class="w-full">
            <path d="M0 40V0C240 30 480 40 720 40C960 40 1200 30 1440 0V40H0Z" fill="rgb(249 250 251 / 0.5)"/>
        </svg>
    </div>
</div>

@if(session('success'))
<div class="max-w-6xl mx-auto mb-6 animate-fade-in-up">
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-5 flex items-center gap-3">
        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div>
            <p class="font-semibold">Suggestion submitted!</p>
            <p class="text-sm text-emerald-600 mt-0.5">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

<div class="max-w-6xl mx-auto animate-fade-in-up" style="animation-delay: 0.1s;">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- FORM COLUMN -->
        <form method="POST" action="/solicitudes" class="lg:col-span-7 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @csrf

            <!-- Form Header -->
            <div class="bg-gradient-to-r from-gray-50 to-white px-8 py-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <div class="w-7 h-7 rounded-full bg-ans-dark-green text-white text-xs font-bold flex items-center justify-center">1</div>
                        <div class="w-12 h-0.5 bg-ans-dark-green rounded-full"></div>
                        <div class="w-7 h-7 rounded-full bg-ans-dark-green text-white text-xs font-bold flex items-center justify-center">2</div>
                        <div class="w-12 h-0.5 bg-ans-dark-green rounded-full"></div>
                        <div class="w-7 h-7 rounded-full bg-ans-dark-green text-white text-xs font-bold flex items-center justify-center">3</div>
                    </div>
                    <span class="text-xs text-gray-400 ml-2 font-medium">All fields required</span>
                </div>
            </div>

            <div class="p-8 space-y-8">

                <!-- Step 1: Your Info -->
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-ans-dark-green/10 text-ans-dark-green text-[10px] font-bold flex items-center justify-center">1</span>
                        Your Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="requester_name" required value="{{ old('requester_name', Auth::user()->name ?? '') }}"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green focus:bg-white transition-all @error('requester_name') border-red-400 bg-red-50 @enderror"
                                placeholder="John Doe">
                            @error('requester_name')<p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Institutional Email</label>
                            <input type="email" name="requester_email" required value="{{ old('requester_email', Auth::user()->email ?? '') }}"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green focus:bg-white transition-all @error('requester_email') border-red-400 bg-red-50 @enderror"
                                placeholder="jdoe@ans.edu.ni">
                            @error('requester_email')<p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-200"></div>

                <!-- Step 2: Tool Details -->
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-ans-dark-green/10 text-ans-dark-green text-[10px] font-bold flex items-center justify-center">2</span>
                        Tool Details
                    </h3>
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tool Name</label>
                            <input type="text" id="input-name" name="tool_name" required value="{{ old('tool_name') }}"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green focus:bg-white transition-all"
                                placeholder="e.g. ChatGPT, Gamma, Canva AI...">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Official URL</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                </div>
                                <input type="url" name="url" required value="{{ old('url') }}" placeholder="https://example.com"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green focus:bg-white transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea id="input-desc" name="description" required rows="4" placeholder="Briefly describe what this tool does and how it can be used in the classroom..."
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green focus:bg-white transition-all resize-none">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-200"></div>

                <!-- Step 3: Classification -->
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-ans-dark-green/10 text-ans-dark-green text-[10px] font-bold flex items-center justify-center">3</span>
                        Classification
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                            <select name="category" required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green focus:bg-white transition-all appearance-none cursor-pointer">
                                <option value="">Select a category...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}" {{ old('category') === $category->name ? 'selected' : '' }}>
                                        {{ $category->icon }} {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end pb-1">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" id="input-workspace" name="is_google_workspace" value="1" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-ans-dark-green transition-all"></div>
                                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm peer-checked:translate-x-5 transition-transform"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">Google Workspace tool</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-white border-t border-gray-100">
                <button type="submit"
                    class="w-full bg-gradient-to-r from-ans-dark-green to-ans-seal-green hover:from-ans-seal-green hover:to-ans-dark-green text-white font-bold py-4 rounded-xl shadow-lg shadow-ans-dark-green/20 hover:shadow-xl transition-all duration-300 text-sm tracking-wide flex items-center justify-center gap-2 hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    Submit Suggestion
                </button>
                <p class="text-center text-xs text-gray-400 mt-3">Your suggestion will be reviewed by the admin team before being published.</p>
            </div>
        </form>

        <!-- LIVE MOCKUP COLUMN -->
        <div class="lg:col-span-5 sticky top-6 hidden lg:block">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Live Card Mockup</p>
            <div class="group bg-white rounded-2xl border border-gray-100 p-6 shadow-xl relative overflow-hidden transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div id="mock-logo-preview" class="w-12 h-12 bg-gradient-to-br from-ans-dark-green/10 to-ans-light-green/5 rounded-xl flex items-center justify-center text-ans-dark-green font-bold text-xl shadow-sm border border-gray-50">
                        N
                    </div>
                    <svg class="w-5 h-5 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </div>
                <h4 id="mock-name" class="font-heading font-bold text-gray-900 text-lg leading-tight">New Tool</h4>
                <p id="mock-desc" class="text-sm text-gray-500 mt-2 leading-relaxed line-clamp-2">Fill in the description on the left to see it update live here...</p>
                <div class="mt-5 flex items-center gap-2">
                    <span id="mock-type" class="text-[10px] font-bold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full uppercase tracking-wider">3rd Party</span>
                    <span class="text-[10px] text-gray-300 ml-auto">Click for details</span>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Real-time Mockup Bindings
const inputName = document.getElementById('input-name');
const inputDesc = document.getElementById('input-desc');
const inputWorkspace = document.getElementById('input-workspace');

const mockName = document.getElementById('mock-name');
const mockDesc = document.getElementById('mock-desc');
const mockType = document.getElementById('mock-type');
const mockLogo = document.getElementById('mock-logo-preview');

inputName.addEventListener('input', e => {
    const val = e.target.value.trim();
    mockName.textContent = val || 'New Tool';
    mockLogo.textContent = (val ? val.charAt(0).toUpperCase() : 'N');
});

inputDesc.addEventListener('input', e => {
    mockDesc.textContent = e.target.value.trim() || 'Fill in the description on the left to see it update live here...';
});

inputWorkspace.addEventListener('change', e => {
    if (e.target.checked) {
        mockType.textContent = 'Workspace';
        mockType.className = 'text-[10px] font-bold bg-ans-blue/10 text-ans-blue px-2.5 py-1 rounded-full uppercase tracking-wider';
    } else {
        mockType.textContent = '3rd Party';
        mockType.className = 'text-[10px] font-bold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full uppercase tracking-wider';
    }
});
</script>
@endsection
