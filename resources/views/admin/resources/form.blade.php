@extends('layouts.app')

@section('title', $resource->exists ? 'Edit Resource' : 'Add Resource')

@section('content')
<div class="propose-page">
    <div class="propose-card">
        <div class="propose-header">
            <a href="{{ route('admin.resources.index') }}" class="propose-back">← Back to Resource Management</a>
            <div class="propose-header__icon">🛡️</div>
            <h1 class="propose-title">
                {{ $resource->exists ? 'Edit Resource' : 'Add New Resource' }}
            </h1>
            <p class="propose-subtitle">Admin — changes apply immediately.</p>
        </div>

        <form method="POST"
              action="{{ $resource->exists ? route('admin.resources.update', $resource) : route('admin.resources.store') }}"
              class="propose-form">
            @csrf
            @if($resource->exists) @method('PUT') @endif

            {{-- Title --}}
            <div class="propose-field">
                <label class="propose-label" for="title">Title <span class="propose-required">*</span></label>
                <input type="text" name="title" id="title" required
                       class="propose-input @error('title') propose-input--error @enderror"
                       value="{{ old('title', $resource->title) }}"
                       placeholder="Resource title">
                @error('title')<p class="propose-error">{{ $message }}</p>@enderror
            </div>

            {{-- Description --}}
            <div class="propose-field">
                <label class="propose-label" for="description">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="propose-input propose-textarea"
                          placeholder="What does this resource cover?">{{ old('description', $resource->description) }}</textarea>
            </div>

            {{-- URL --}}
            <div class="propose-field">
                <label class="propose-label" for="url">URL</label>
                <input type="url" name="url" id="url"
                       class="propose-input" value="{{ old('url', $resource->url) }}"
                       placeholder="https://...">
            </div>

            {{-- Type + Area --}}
            <div class="propose-row">
                <div class="propose-field">
                    <label class="propose-label" for="type">Type <span class="propose-required">*</span></label>
                    <select name="type" id="type" class="propose-input propose-select" required>
                        @foreach(['link'=>'🔗 Link','book'=>'📖 Book','video'=>'🎥 Video','article'=>'📰 Article','tool'=>'🛠️ Tool','course'=>'🎓 Course'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type', $resource->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="propose-field">
                    <label class="propose-label" for="area">Area <span class="propose-required">*</span></label>
                    <select name="area" id="area" class="propose-input propose-select" required>
                        @foreach(['stem'=>'STEM','innovation'=>'Innovation','ai'=>'Artificial Intelligence','robotics'=>'Robotics','design'=>'Design','programming'=>'Programming','math'=>'Mathematics','science'=>'Science','general'=>'General'] as $val => $label)
                            <option value="{{ $val }}" {{ old('area', $resource->area) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Author + Source --}}
            <div class="propose-row">
                <div class="propose-field">
                    <label class="propose-label" for="author">Author</label>
                    <input type="text" name="author" id="author"
                           class="propose-input" value="{{ old('author', $resource->author) }}"
                           placeholder="Author name">
                </div>
                <div class="propose-field">
                    <label class="propose-label" for="source">Source / Platform</label>
                    <input type="text" name="source" id="source"
                           class="propose-input" value="{{ old('source', $resource->source) }}"
                           placeholder="e.g. Khan Academy">
                </div>
            </div>

            {{-- Thumbnail --}}
            <div class="propose-field">
                <label class="propose-label" for="thumbnail_url">Thumbnail URL</label>
                <input type="url" name="thumbnail_url" id="thumbnail_url"
                       class="propose-input" value="{{ old('thumbnail_url', $resource->thumbnail_url) }}"
                       placeholder="https://...">
            </div>

            {{-- Target roles --}}
            <div class="propose-field">
                <label class="propose-label">Target Audience</label>
                <div class="propose-check-group">
                    @foreach(['all'=>'🌐 Everyone','student'=>'🎓 Students','teacher'=>'👩‍🏫 Teachers'] as $val => $label)
                        <label class="propose-checkbox">
                            <input type="checkbox" name="target_roles[]" value="{{ $val }}"
                                {{ in_array($val, old('target_roles', $resource->target_roles ?? ['all'])) ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Published toggle --}}
            <div class="propose-field">
                <label class="propose-checkbox" style="align-items:center; gap:10px;">
                    <input type="checkbox" name="is_published" value="1"
                        {{ old('is_published', $resource->is_published) ? 'checked' : '' }}
                        style="accent-color:#007934; width:18px; height:18px;">
                    <span style="font-size:14px; color:#374151;">Publish immediately</span>
                </label>
                <p style="font-size:12px; color:#6b7280; margin-left:28px;">Unchecked = pending review.</p>
            </div>

            <div class="propose-actions">
                <a href="{{ route('admin.resources.index') }}" class="propose-cancel">Cancel</a>
                <button type="submit" class="propose-submit">
                    {{ $resource->exists ? '💾 Save Changes' : '🚀 Create Resource' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
