@extends('layouts.app')

@section('title', auth()->user()->isAdmin() ? 'Add Resource — Learning Hub' : 'Propose a Resource — Learning Hub')

@section('content')
<div class="propose-page">
    <div class="propose-card">
        {{-- Header --}}
        <div class="propose-header">
            <a href="{{ route('resources.index') }}" class="propose-back">← Back to Learning Hub</a>
            <div class="propose-header__icon">
                {{ auth()->user()->isAdmin() ? '🛡️' : '📝' }}
            </div>
            <h1 class="propose-title">
                {{ auth()->user()->isAdmin() ? 'Add a New Resource' : 'Propose a Resource' }}
            </h1>
            <p class="propose-subtitle">
                @if(auth()->user()->isAdmin())
                    Resource will be published immediately.
                @else
                    Your proposal will be reviewed by an admin before being published.
                @endif
            </p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('resources.propose.store') }}" class="propose-form">
            @csrf

            {{-- Title --}}
            <div class="propose-field">
                <label class="propose-label" for="title">Resource Title <span class="propose-required">*</span></label>
                <input type="text" name="title" id="title"
                       class="propose-input @error('title') propose-input--error @enderror"
                       value="{{ old('title') }}"
                       placeholder="e.g. MIT OpenCourseWare — Intro to Computer Science"
                       required>
                @error('title')<p class="propose-error">{{ $message }}</p>@enderror
            </div>

            {{-- Description --}}
            <div class="propose-field">
                <label class="propose-label" for="description">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="propose-input propose-textarea"
                          placeholder="Briefly explain what this resource covers and who it's for...">{{ old('description') }}</textarea>
                @error('description')<p class="propose-error">{{ $message }}</p>@enderror
            </div>

            {{-- URL --}}
            <div class="propose-field">
                <label class="propose-label" for="url">Resource URL</label>
                <input type="url" name="url" id="url"
                       class="propose-input @error('url') propose-input--error @enderror"
                       value="{{ old('url') }}"
                       placeholder="https://...">
                @error('url')<p class="propose-error">{{ $message }}</p>@enderror
            </div>

            {{-- Type + Area row --}}
            <div class="propose-row">
                <div class="propose-field">
                    <label class="propose-label" for="type">Type <span class="propose-required">*</span></label>
                    <select name="type" id="type" class="propose-input propose-select" required>
                        <option value="">Select type...</option>
                        @foreach(['link'=>'🔗 Link','book'=>'📖 Book','video'=>'🎥 Video','article'=>'📰 Article','tool'=>'🛠️ Tool','course'=>'🎓 Course'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')<p class="propose-error">{{ $message }}</p>@enderror
                </div>

                <div class="propose-field">
                    <label class="propose-label" for="area">Knowledge Area <span class="propose-required">*</span></label>
                    <select name="area" id="area" class="propose-input propose-select" required>
                        <option value="">Select area...</option>
                        @foreach(['stem'=>'STEM','innovation'=>'Innovation','ai'=>'Artificial Intelligence','robotics'=>'Robotics','design'=>'Design','programming'=>'Programming','math'=>'Mathematics','science'=>'Science','general'=>'General'] as $val => $label)
                            <option value="{{ $val }}" {{ old('area') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('area')<p class="propose-error">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Author + Source row --}}
            <div class="propose-row">
                <div class="propose-field">
                    <label class="propose-label" for="author">Author / Creator</label>
                    <input type="text" name="author" id="author"
                           class="propose-input" value="{{ old('author') }}"
                           placeholder="e.g. David J. Malan">
                </div>
                <div class="propose-field">
                    <label class="propose-label" for="source">Platform / Source</label>
                    <input type="text" name="source" id="source"
                           class="propose-input" value="{{ old('source') }}"
                           placeholder="e.g. Harvard / edX">
                </div>
            </div>

            {{-- Thumbnail URL --}}
            <div class="propose-field">
                <label class="propose-label" for="thumbnail_url">Thumbnail URL <span class="propose-hint">(optional — auto-shown on card)</span></label>
                <input type="url" name="thumbnail_url" id="thumbnail_url"
                       class="propose-input" value="{{ old('thumbnail_url') }}"
                       placeholder="https://...">
            </div>

            {{-- Target roles --}}
            <div class="propose-field">
                <label class="propose-label">Target Audience <span class="propose-required">*</span></label>
                <div class="propose-check-group">
                    @foreach(['all'=>'🌐 Everyone','student'=>'🎓 Students','teacher'=>'👩‍🏫 Teachers'] as $val => $label)
                        <label class="propose-checkbox">
                            <input type="checkbox" name="target_roles[]" value="{{ $val }}"
                                {{ in_array($val, old('target_roles', ['all'])) ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('target_roles')<p class="propose-error">{{ $message }}</p>@enderror
            </div>

            {{-- Submit --}}
            <div class="propose-actions">
                <a href="{{ route('resources.index') }}" class="propose-cancel">Cancel</a>
                <button type="submit" class="propose-submit">
                    {{ auth()->user()->isAdmin() ? '🚀 Publish Resource' : '📬 Submit for Review' }}
                </button>
    .propose-page { max-width: 760px; margin: 0 auto; padding: 40px 24px 80px; }
.propose-card {
    background: #ffffff; border: 1px solid #e5e7eb;
    border-radius: 20px; overflow: hidden;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
.propose-header {
    background: linear-gradient(135deg, #003a1c, #005f2e);
    padding: 32px 32px 28px; text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.propose-back { font-size: 13px; color: rgba(255,255,255,0.7); text-decoration: none; display: block; margin-bottom: 16px; }
.propose-back:hover { color: #FF8300; }
.propose-header__icon { font-size: 40px; margin-bottom: 10px; }
.propose-title { font-family: 'Montserrat', sans-serif; font-size: 24px; font-weight: 700; color: #fff; margin-bottom: 8px; }
.propose-subtitle { font-size: 14px; color: rgba(255,255,255,0.65); }

.propose-form { padding: 28px 32px; display: flex; flex-direction: column; gap: 20px; }
.propose-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 600px) { .propose-row { grid-template-columns: 1fr; } }

.propose-field { display: flex; flex-direction: column; gap: 6px; }
.propose-label { font-size: 13px; font-weight: 600; color: #374151; }
.propose-required { color: #FF8300; }
.propose-hint { font-size: 11px; color: #6b7280; font-weight: 400; }
.propose-input {
    background: #ffffff; border: 1px solid #d1d5db;
    border-radius: 10px; color: #1f2937; font-size: 14px;
    padding: 10px 14px; outline: none; transition: border-color 0.2s;
    font-family: 'Inter', sans-serif;
}
.propose-input::placeholder { color: #9ca3af; }
.propose-input:focus { border-color: #007934; background: #ffffff; box-shadow: 0 0 0 3px rgba(0, 121, 52, 0.1); }
.propose-input--error { border-color: rgba(239,68,68,0.5); }
.propose-textarea { resize: vertical; min-height: 80px; }
.propose-select { cursor: pointer; }
.propose-select option { background: #ffffff; color: #1f2937; }
.propose-error { font-size: 12px; color: #ef4444; }

.propose-check-group { display: flex; flex-wrap: wrap; gap: 12px; }
.propose-checkbox { display: flex; align-items: center; gap: 8px; cursor: pointer; }
.propose-checkbox input { accent-color: #007934; width: 16px; height: 16px; }
.propose-checkbox span { font-size: 14px; color: #4b5563; }

.propose-actions { display: flex; justify-content: flex-end; gap: 12px; padding-top: 8px; }
.propose-cancel {
    padding: 11px 22px; border-radius: 10px; font-size: 14px;
    background: #f3f4f6; border: 1px solid #e5e7eb;
    color: #4b5563; text-decoration: none; transition: all 0.2s;
}
.propose-cancel:hover { background: #e5e7eb; color: #111827; }
.propose-submit {
    padding: 11px 24px; border-radius: 10px; font-size: 14px; font-weight: 600;
    background: linear-gradient(135deg, #007934, #00a04a);
    color: #fff; border: none; cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,121,52,0.15); transition: all 0.2s;
}
.propose-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,121,52,0.25); }
</style>
@endsection
