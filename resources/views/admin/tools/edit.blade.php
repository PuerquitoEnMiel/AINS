@extends('layouts.app')
@section('header-title') Edit Tool @endsection
@section('header-subtitle') Update details for "{{ $tool->name }}". @endsection

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.tools.index') }}" class="inline-flex items-center gap-2 text-sm text-ans-dark-green hover:underline font-medium group">
            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to catalog
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- FORM COLUMN -->
        <form method="POST" action="{{ route('admin.tools.update', $tool) }}" enctype="multipart/form-data" 
              class="lg:col-span-7 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up">
            @csrf @method('PUT')

            <div class="bg-gradient-to-r from-ans-blue/5 to-white px-8 py-5 border-b border-gray-100 flex items-center gap-4">
                @if($tool->logo_url)
                    <img src="{{ asset($tool->logo_url) }}" alt="{{ $tool->name }}" class="w-12 h-12 rounded-xl object-cover border border-gray-100">
                @else
                    <div class="w-12 h-12 bg-ans-dark-green/10 rounded-xl flex items-center justify-center text-ans-dark-green font-bold text-lg">{{ substr($tool->name, 0, 1) }}</div>
                @endif
                <div>
                    <h2 class="text-xl font-heading font-bold text-gray-900">{{ $tool->name }}</h2>
                    <p class="text-sm text-gray-500">Editing tool #{{ $tool->id }}</p>
                </div>
            </div>

            <div class="p-8 space-y-6">
                <!-- Logo Upload (Drag & Drop Zone) -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">Tool Logo / Image</label>
                    <div id="drop-zone" class="flex flex-col sm:flex-row items-center gap-5 p-5 border-2 border-dashed border-gray-200 hover:border-ans-dark-green/40 hover:bg-ans-dark-green/5 rounded-2xl transition-all duration-300 cursor-pointer">
                        <div id="logo-preview" class="w-20 h-20 bg-gray-100 rounded-2xl overflow-hidden flex-shrink-0 border border-gray-100 bg-white shadow-sm transition-transform duration-300">
                            @if($tool->logo_url)
                                <img src="{{ asset($tool->logo_url) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                        </div>
                        <div class="text-center sm:text-left flex-1">
                            <label for="logo-input" class="cursor-pointer inline-flex items-center gap-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-semibold text-xs px-3.5 py-2 rounded-xl transition-all shadow-sm">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                Change Image
                            </label>
                            <input id="logo-input" type="file" name="logo" accept="image/*" class="sr-only" onchange="previewLogo(this)">
                            <p class="text-xs text-gray-400 mt-2">PNG, JPG up to 2MB. Drag & drop to update.</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-200"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tool Name *</label>
                        <input type="text" id="input-name" name="name" required value="{{ old('name', $tool->name) }}"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-blue/20 focus:border-ans-blue focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Official URL *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            </div>
                            <input type="url" name="url" required value="{{ old('url', $tool->url) }}"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-blue/20 focus:border-ans-blue focus:bg-white transition-all">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Description *</label>
                    <textarea id="input-desc" name="description" required rows="3"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-blue/20 focus:border-ans-blue focus:bg-white transition-all resize-none">{{ old('description', $tool->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Category *</label>
                        <select name="category_id" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-blue/20 focus:border-ans-blue focus:bg-white transition-all appearance-none">
                            <option value="">Select...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $tool->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Status *</label>
                        <select name="approval_status" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-blue/20 focus:border-ans-blue focus:bg-white transition-all appearance-none">
                            <option value="approved" {{ old('approval_status', $tool->approval_status) === 'approved' ? 'selected' : '' }}>✓ Published</option>
                            <option value="pending" {{ old('approval_status', $tool->approval_status) === 'pending' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-3 justify-end pb-1">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" id="input-workspace" name="is_google_workspace" value="1" class="sr-only peer" {{ old('is_google_workspace', $tool->is_google_workspace) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-ans-dark-green transition-all"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm peer-checked:translate-x-5 transition-transform"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Workspace Platform</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" id="input-official" name="is_official" value="1" class="sr-only peer" {{ old('is_official', $tool->is_official) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-ans-orange transition-all"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm peer-checked:translate-x-5 transition-transform"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Official Tool</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-white border-t border-gray-100 flex gap-3">
                <button type="submit" class="flex-1 bg-gradient-to-r from-ans-blue to-ans-light-blue text-white font-bold py-3.5 rounded-xl shadow-lg shadow-ans-blue/20 hover:shadow-xl transition-all text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Save Changes
                </button>
                <a href="{{ route('admin.tools.index') }}" class="px-6 py-3.5 bg-white text-gray-500 border border-gray-200 font-semibold rounded-xl text-sm hover:bg-gray-50 transition-all text-center">Cancel</a>
            </div>
        </form>

        <!-- LIVE MOCKUP COLUMN -->
        <div class="lg:col-span-5 sticky top-6 hidden lg:block">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Live Card Mockup</p>
            <div class="group bg-white rounded-2xl border border-gray-100 p-6 shadow-xl relative overflow-hidden transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div id="mock-logo-preview" class="w-12 h-12 bg-gradient-to-br from-ans-dark-green/10 to-ans-light-green/5 rounded-xl flex items-center justify-center text-ans-dark-green font-bold text-xl shadow-sm border border-gray-50 overflow-hidden">
                        @if($tool->logo_url)
                            <img src="{{ asset($tool->logo_url) }}" class="w-full h-full object-cover">
                        @else
                            {{ substr($tool->name, 0, 1) }}
                        @endif
                    </div>
                    <svg class="w-5 h-5 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </div>
                <h4 id="mock-name" class="font-heading font-bold text-gray-900 text-lg leading-tight">{{ $tool->name }}</h4>
                <p id="mock-desc" class="text-sm text-gray-500 mt-2 leading-relaxed line-clamp-2">{{ $tool->description }}</p>
                <div class="mt-5 flex items-center gap-2">
                    @if($tool->is_google_workspace)
                        <span id="mock-type" class="text-[10px] font-bold bg-ans-blue/10 text-ans-blue px-2.5 py-1 rounded-full uppercase tracking-wider">Workspace</span>
                    @else
                        <span id="mock-type" class="text-[10px] font-bold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full uppercase tracking-wider">3rd Party</span>
                    @endif
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
    if (!mockLogo.querySelector('img')) {
        mockLogo.textContent = (val ? val.charAt(0).toUpperCase() : 'N');
    }
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

// Image Preview & Drag and Drop Setup
const dropZone = document.getElementById('drop-zone');
const logoInput = document.getElementById('logo-input');
const logoPreview = document.getElementById('logo-preview');

function handleImage(file) {
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => {
            logoPreview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            mockLogo.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(file);
    }
}

function previewLogo(input) {
    if (input.files && input.files[0]) {
        handleImage(input.files[0]);
    }
}

// Drop zone click triggers file input
dropZone.addEventListener('click', e => {
    if (e.target !== logoInput && !e.target.closest('label')) {
        logoInput.click();
    }
});

// Drag and drop events
['dragenter', 'dragover'].forEach(name => {
    dropZone.addEventListener(name, e => {
        e.preventDefault();
        dropZone.classList.add('border-ans-dark-green', 'bg-ans-dark-green/5');
    }, false);
});

['dragleave', 'drop'].forEach(name => {
    dropZone.addEventListener(name, e => {
        e.preventDefault();
        dropZone.classList.remove('border-ans-dark-green', 'bg-ans-dark-green/5');
    }, false);
});

dropZone.addEventListener('drop', e => {
    const dt = e.dataTransfer;
    const files = dt.files;
    if (files.length) {
        logoInput.files = files;
        handleImage(files[0]);
    }
}, false);
</script>
@endsection
