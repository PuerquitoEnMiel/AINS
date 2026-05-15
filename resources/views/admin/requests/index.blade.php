@extends('layouts.app')

@section('content')
<div>
    <h2 class="text-3xl font-heading font-bold text-gray-900 mb-2">Panel de Administración</h2>
    <p class="text-gray-500 mb-8">Solicitudes de herramientas enviadas por profesores.</p>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-6">{{ session('success') }}</div>
    @endif
    @if(session('info'))
    <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 mb-6">{{ session('info') }}</div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-6 mb-10">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
            <div class="text-3xl font-heading font-bold text-ans-orange">{{ $requests->where('status','pending')->count() }}</div>
            <p class="text-sm text-gray-500 mt-1">Pendientes</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
            <div class="text-3xl font-heading font-bold text-ans-light-green">{{ $requests->where('status','approved')->count() }}</div>
            <p class="text-sm text-gray-500 mt-1">Aprobadas</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
            <div class="text-3xl font-heading font-bold text-gray-400">{{ $requests->where('status','rejected')->count() }}</div>
            <p class="text-sm text-gray-500 mt-1">Rechazadas</p>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Herramienta</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Profesor</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Categoría</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Estado</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($requests as $req)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-900">{{ $req->tool_name }}</div>
                        <a href="{{ $req->url }}" target="_blank" class="text-xs text-ans-dark-green hover:underline">{{ $req->url }}</a>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $req->description }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800">{{ $req->requester_name }}</div>
                        <div class="text-xs text-gray-500">{{ $req->requester_email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">{{ $req->category }}</span>
                        @if($req->is_google_workspace)
                        <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded text-xs ml-1">Workspace</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($req->status === 'pending')
                        <span class="bg-orange-50 text-ans-orange font-medium px-2 py-1 rounded text-xs">Pendiente</span>
                        @elseif($req->status === 'approved')
                        <span class="bg-green-50 text-green-700 font-medium px-2 py-1 rounded text-xs">Aprobada</span>
                        @else
                        <span class="bg-gray-100 text-gray-500 font-medium px-2 py-1 rounded text-xs">Rechazada</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($req->status === 'pending')
                        <div class="flex gap-2">
                            <form method="POST" action="/admin/solicitudes/{{ $req->id }}/approve">
                                @csrf
                                <button type="submit" class="bg-ans-dark-green text-white text-xs px-3 py-1.5 rounded hover:bg-ans-seal-green transition">
                                    ✓ Aprobar
                                </button>
                            </form>
                            <form method="POST" action="/admin/solicitudes/{{ $req->id }}/reject">
                                @csrf
                                <button type="submit" class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-gray-200 transition">
                                    ✗ Rechazar
                                </button>
                            </form>
                        </div>
                        @else
                        <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">No hay solicitudes todavía.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
