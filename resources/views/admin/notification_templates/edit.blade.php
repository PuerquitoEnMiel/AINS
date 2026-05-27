@extends('layouts.app')

@section('header-title', 'Edit Notification Template')
@section('header-subtitle', 'Modify the subject and message format for ' . $template->type)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <h3 class="text-sm font-semibold text-gray-800">Edit Template: {{ strtoupper($template->type) }}</h3>
            <a href="{{ route('admin.notification-templates.index') }}" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                &larr; Back to templates
            </a>
        </div>

        <form method="POST" action="{{ route('admin.notification-templates.update', $template) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label for="subject" class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Email Subject</label>
                <input type="text" id="subject" name="subject" value="{{ old('subject', $template->subject) }}" required
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-ans-light-green/30 focus:border-ans-light-green outline-none transition-all text-sm">
                @error('subject')
                    <p class="text-xs text-red-600 font-medium mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="template" class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Template Message</label>
                <textarea id="template" name="template" rows="5" required
                          class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-ans-light-green/30 focus:border-ans-light-green outline-none transition-all text-sm font-mono">{{ old('template', $template->template) }}</textarea>
                @error('template')
                    <p class="text-xs text-red-600 font-medium mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl space-y-2">
                <h5 class="text-xs font-bold text-blue-800 uppercase tracking-wider">Available Placeholders</h5>
                <ul class="text-xs text-blue-700 space-y-1 list-disc pl-4 font-normal">
                    <li><code>{user}</code>: The name of the teacher/user trigger.</li>
                    <li><code>{badge}</code>: The name of the badge (only valid for <code>evidence</code> notifications).</li>
                    <li><code>{suggestion}</code>: The name of the suggested badge (only valid for <code>suggestion</code> notifications).</li>
                </ul>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('admin.notification-templates.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-ans-dark-green hover:bg-ans-dark-green/95 text-white text-xs font-semibold rounded-xl transition-all shadow-sm">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
