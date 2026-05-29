@extends('layouts.app')

@section('title', 'Manage Resources — Admin')

@section('content')
<div class="admin-res-page">
    {{-- Header --}}
    <div class="admin-res-header">
        <div>
            <h1 class="admin-res-title">📚 Manage Resources</h1>
            <p class="admin-res-subtitle">Learning Hub — STEM &amp; Innovation content management</p>
        </div>
        <a href="{{ route('resources.propose') }}" class="admin-res-btn-add">+ Add Resource</a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="admin-res-flash">✅ {{ session('success') }}</div>
    @endif

    {{-- Status filter tabs --}}
    <div class="admin-res-tabs">
        @foreach(['all'=>'All', 'pending'=>'⏳ Pending', 'published'=>'✅ Published', 'trashed'=>'🗑 Deleted'] as $val => $label)
            <a href="{{ route('admin.resources.index', ['status'=>$val]) }}"
               class="admin-res-tab {{ $status === $val ? 'admin-res-tab--active' : '' }}">
                {{ $label }}
                @if($val === 'pending' && $pendingCount > 0)
                    <span class="admin-res-tab-badge">{{ $pendingCount }}</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="admin-res-table-wrap">
        <table class="admin-res-table">
            <thead>
                <tr>
                    <th>Resource</th>
                    <th>Type</th>
                    <th>Area</th>
                    <th>Created By</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resources as $resource)
                <tr class="admin-res-row {{ !$resource->is_published && !$resource->trashed() ? 'admin-res-row--pending' : '' }}">
                    <td class="admin-res-cell-title">
                        <div class="admin-res-cell-title__icon area-{{ $resource->area }}">{{ $resource->typeIcon() }}</div>
                        <div>
                            <p class="admin-res-cell-title__name">{{ Str::limit($resource->title, 55) }}</p>
                            @if($resource->source)
                                <p class="admin-res-cell-title__source">{{ $resource->source }}</p>
                            @endif
                        </div>
                    </td>
                    <td><span class="resource-badge resource-badge--type">{{ $resource->typeIcon() }} {{ ucfirst($resource->type) }}</span></td>
                    <td><span class="resource-badge resource-badge--{{ $resource->area }}">{{ $resource->areaLabel() }}</span></td>
                    <td class="admin-res-cell-author">{{ $resource->creator?->name ?? '—' }}</td>
                    <td>
                        @if($resource->trashed())
                            <span class="admin-res-status admin-res-status--trashed">Deleted</span>
                        @elseif($resource->is_published)
                            <span class="admin-res-status admin-res-status--published">Published</span>
                        @else
                            <span class="admin-res-status admin-res-status--pending">Pending</span>
                        @endif
                    </td>
                    <td class="admin-res-cell-actions">
                        @if(!$resource->trashed())
                            @if(!$resource->is_published)
                                <form method="POST" action="{{ route('admin.resources.approve', $resource) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="admin-res-action admin-res-action--approve" title="Approve">✓ Approve</button>
                                </form>
                            @endif
                            <a href="{{ route('admin.resources.edit', $resource) }}" class="admin-res-action admin-res-action--edit" title="Edit">✏️</a>
                            <a href="{{ route('resources.show', $resource) }}" class="admin-res-action" title="Preview" target="_blank">👁</a>
                            <form method="POST" action="{{ route('admin.resources.destroy', $resource) }}" style="display:inline;"
                                  onsubmit="return confirm('Delete this resource?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-res-action admin-res-action--delete" title="Delete">🗑</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="admin-res-empty">No resources found for this filter.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-res-pagination">{{ $resources->links() }}</div>
</div>

<style>
.admin-res-page { max-width: 1200px; margin: 0 auto; padding: 32px 24px 80px; }
.admin-res-header {
    display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;
    margin-bottom: 24px; flex-wrap: wrap;
}
.admin-res-title { font-family: 'Montserrat', sans-serif; font-size: 26px; font-weight: 700; color: #111827; }
.admin-res-subtitle { font-size: 13px; color: #6b7280; margin-top: 4px; }
.admin-res-btn-add {
    background: linear-gradient(135deg, #007934, #00a04a); color: #fff;
    padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 600;
    text-decoration: none; white-space: nowrap; transition: all 0.2s;
    box-shadow: 0 4px 15px rgba(0,121,52,0.15);
}
.admin-res-btn-add:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,121,52,0.25); }
.admin-res-flash {
    background: rgba(0,135,74,0.1); border: 1px solid rgba(0,135,74,0.3);
    color: #007934; border-radius: 10px; padding: 12px 16px;
    font-size: 14px; margin-bottom: 20px;
}

/* Tabs */
.admin-res-tabs { display: flex; gap: 6px; margin-bottom: 20px; flex-wrap: wrap; }
.admin-res-tab {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 500;
    text-decoration: none; color: #4b5563;
    border: 1px solid #d1d5db; background: #ffffff; transition: all 0.2s;
}
.admin-res-tab:hover { color: #111827; background: #f3f4f6; }
.admin-res-tab--active { color: #007934; background: #ffffff; border-color: #007934; font-weight: 600; }
.admin-res-tab-badge {
    background: rgba(255,131,0,0.15); color: #d97706;
    font-size: 10px; font-weight: 700; padding: 1px 7px; border-radius: 99px;
}

/* Table */
.admin-res-table-wrap {
    background: #ffffff; border: 1px solid #e5e7eb;
    border-radius: 16px; overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
}
.admin-res-table { width: 100%; border-collapse: collapse; }
.admin-res-table thead tr {
    background: #f9fafb; border-bottom: 1px solid #e5e7eb;
}
.admin-res-table th {
    padding: 12px 16px; font-size: 11px; font-weight: 700;
    color: #4b5563; text-transform: uppercase; letter-spacing: 0.5px;
    text-align: left;
}
.admin-res-row {
    border-bottom: 1px solid #f3f4f6; transition: background 0.15s;
}
.admin-res-row:hover { background: #f9fafb; }
.admin-res-row--pending { background: rgba(255,131,0,0.02); }
.admin-res-table td { padding: 12px 16px; }

.admin-res-cell-title { display: flex; align-items: center; gap: 12px; }
.admin-res-cell-title__icon {
    width: 36px; height: 36px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
}
.admin-res-cell-title__name { font-size: 14px; color: #111827; font-weight: 600; }
.admin-res-cell-title__source { font-size: 12px; color: #6b7280; margin-top: 2px; }
.admin-res-cell-author { font-size: 13px; color: #4b5563; }
.admin-res-cell-actions { display: flex; align-items: center; gap: 6px; }

/* Status badges */
.admin-res-status {
    font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 99px;
}
.admin-res-status--published { background: rgba(0,121,52,0.1); color: #007934; border: 1px solid rgba(0,121,52,0.2); }
.admin-res-status--pending   { background: rgba(255,131,0,0.1); color: #d97706; border: 1px solid rgba(255,131,0,0.2); }
.admin-res-status--trashed   { background: rgba(239,68,68,0.1); color: #dc2626; border: 1px solid rgba(239,68,68,0.2); }

/* Action buttons */
.admin-res-action {
    background: #ffffff; border: 1px solid #d1d5db;
    color: #4b5563; border-radius: 7px; padding: 5px 10px;
    font-size: 13px; cursor: pointer; text-decoration: none; transition: all 0.2s;
}
.admin-res-action:hover { background: #f3f4f6; color: #111827; }
.admin-res-action--approve { color: #007934; border-color: rgba(0,121,52,0.2); font-size: 12px; font-weight: 600; }
.admin-res-action--approve:hover { background: rgba(0,121,52,0.05); }
.admin-res-action--edit { color: #2563eb; }
.admin-res-action--delete { color: #dc2626; border-color: rgba(239,68,68,0.2); }
.admin-res-action--delete:hover { background: rgba(239,68,68,0.05); }

.admin-res-empty { text-align: center; padding: 40px; color: #9ca3af; font-size: 14px; }
.admin-res-pagination { display: flex; justify-content: center; margin-top: 24px; }
</style>
@endsection
